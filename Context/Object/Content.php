<?php
/**
 * File containing the User object context
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Context\Object;

use Behat\Gherkin\Node\TableNode;
use Behat\Gherkin\Node\PyStringNode;
use PHPUnit_Framework_Assert as Assertion;
use EzSystems\BehatBundle\Helper\Gherkin;

/**
 * Sentences for Content
 */
trait Content
{
    /**
     * Get the value (contentId) corresponding to name $name from KeyMap variable.
     * Tests that content id is not null (not found).
     *
     * @param  string $name  key/name to search for.
     *
     * @return int           content Id
     */
    protected function getContentByNameMap( $name )
    {
        $content = $this->getValuesFromKeyMap( $name );
        Assertion::assertNotNull(
            $content,
            "Couldn't find content '$name' in current scenario."
        );
        return $content;
    }

    /**
     * @Given there is a(n) content :name of (content) type :contentType at (location) :location with (the) (following) fields:
     * @Given there is a(n) :name content of (content) type :contentType at (location) :location with (the) (following) fields:
     *
     * Makes sure a content of type 'contentType' exists at/under parent location ':location' with the provided fields.
     * If it does not exist, a new one is created. 'name' is mapped to this content for the current scenario.
     */
    public function givenThereIsContentAtLocationWithFields( $name, $contentType, $locationPath, PyStringNode $fieldData )
    {
        $locationId = $this->getContentManager()->loadLocationByPathString( $locationPath );

        $contentType = strtolower( $contentType );
        $fields = Gherkin::getMultilineTable( $fieldData );

        $content = $this->getContentManager()->ensureContentExistsWithFields( $contentType, $fields, $locationId );

        $this->addValuesToKeyMap( $name, $content );
    }

    /**
     * @Given there is a(n) content :name of (content) type :contentType under/in :parentName with (the) (following) fields:
     * @Given there is a(n) :name content of (content) type :contentType under/in :parentName with (the) (following) fields:
     *
     * Makes sure a content of type 'contentType' exists in/under parent content 'parentName' with the provided fields.
     * If it does not exist, a new one is created. 'name' is mapped to this content for the current scenario.
     */
    public function givenThereIsContentUnderParentWithFields( $name, $contentType, $parentName, PyStringNode $fieldData )
    {
        // get parent location from name map
        $parentContent = $this->getContentByNameMap( $parentName );
        $parentLocationId = $parentContent->contentInfo->mainLocationId;


        $identifier = strtolower( $contentType );
        $fields = Gherkin::getMultilineTable( $fieldData );

        $content = $this->getContentManager()->ensureContentExistsWithFields( $identifier, $fields, $parentLocationId );

        $this->addValuesToKeyMap( $name, $content );
    }


    /**
     * @Given (a) content with location :location doesnt (already) exist
     * @Given (a) content with location :location does not (already) exist
     *
     * Makes sure there is no content at location 'location'.
     * If there is one, it is removed.
     */
    public function givenContentDoesntExistAtLocation( $locationPath )
    {
        $locationId = $this->getContentManager()->loadLocationByPathString( $locationPath );

        if ( $locationId )
        {
            $this->getContentManager()->removeContentWithLocationId( $locationId );
        }
    }

    /**
     * @Given content :name is updated with fields:
     *
     * Updates content mapped by 'name' with the fields in the provided field data.
     * Content fields have an identifier and a body, separated by at least three equal ('=') signs.
     * Fields are separated by an empty line ('\n'), if the line after is a new field identifier. Example:
     *
     *      """
     *      title
     *      =======
     *      Test Content
     * \n
     *      other identifier
     *      ================
     *      <?xml version="1.0" encoding="utf-8"?>
     *      <section xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/" xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/">
     *
     *          <paragraph>Test content</paragraph>
     *
     *      </section>
     *      """
     */
    public function givenContentIsUpdated( $name, PyStringNode $fieldData )
    {
        $content = $this->getContentByNameMap( $name );
        $fields = Gherkin::getMultilineTable( $fieldData );

        $this->getContentManager()->updateContent( $content->id, $fields );
    }

    /**
     * @Given content :name is removed
     *
     * Removes content mapped by 'name' (the object-name mapping is kept for the current scenario).
     */
    public function givenContentIsRemoved( $name )
    {
        $content = $this->getContentByNameMap( $name );

        $this->getContentManager()->removeContent( $content->id );
    }

    /**
     * @Then (a) content :name exists
     *
     * Tests that content mapped by 'name' exists.
     */
    public function thenContentExists( $name, $locationPath )
    {
        $content = $this->getContentByNameMap( $name );

        Assertion::assertTrue(
            $this->getContentManager()->checkContentExists( $content->id ),
            "The content '$name' was not found."
        );
    }

    /**
     * @Then (a) content :name does not exist (any longer)
     * @Then (a) content :name doesnt exist (any longer)
     *
     * Checks that content mapped by 'name' does not exist
     */
    public function thenContentDoesNotExist( $name )
    {
        $content = $this->getContentByNameMap( $name );

        Assertion::assertFalse(
            $this->getContentManager()->checkContentExists( $content->id ),
            "The content '$name' was not found."
        );
    }

    /**
     * @Then (a) content :name exists under :parentName
     *
     * Checks that content mapped by 'name' exists under location of content mapped by 'parentName'
     */
    public function thenContentExistsUnder( $name, $parentName )
    {
        $content = $this->getContentByNameMap( $name );

        // get parent location from name map
        $parentContent = $this->getContentByNameMap( $parentName );
        $parentLocationId = $parentContent->contentInfo->mainLocationId;

        Assertion::assertTrue(
            $this->getContentManager()->checkContentExistsAtLocation( $content->id, $parentLocationId ),
            "The content '$name' was not found under '$parentName'"
        );
    }

    /**
     * @Then (a) content :name exists at (location) :location
     *
     * Checks that content mapped by 'name' exists at/under location path 'location'
     */
    public function thenContentExistsAtLocation( $name, $locationPath )
    {
        $content = $this->getContentByNameMap( $name );

        $locationId = $this->getContentManager()->loadLocationByPathString( $locationPath );
        Assertion::assertNotNull(
            $locationId,
            "No content found at location '$locationPath'"
        );

        Assertion::assertTrue(
            $this->getContentManager()->checkContentExistsAtLocation( $content->id, $locationId ),
            "The content '$name' was not found at location '$locationPath'"
        );
    }

    /**
     * @Then (a) content :name exists with location :location
     *
     * Tests that content mapped by 'name' exists with location path 'location'
     */
    public function thenContentExistsWithLocation( $name, $locationPath )
    {
        $content = $this->getContentByNameMap( $name );

        $locationId = $this->getContentManager()->loadLocationByPathString( $locationPath );
        Assertion::assertNotNull(
            $locationId,
            "No content found at location '$locationPath'"
        );

        Assertion::assertTrue(
            $this->getContentManager()->checkContentExistsWithLocation( $content->id, $locationId ),
            "The content '$name' was not found at location '$locationPath'"
        );
    }

    /**
     * @Then (a) content does not exist with location :location
     *
     * Tests that no content exists with location path 'location'
     */
    public function thenContentDoesNotExistWithLocation( $locationPath )
    {
        $locationId = $this->getContentManager()->loadLocationByPathString( $locationPath );

        Assertion::assertFalse(
            $locationId,
            "Unexpected content was found at location '$locationPath'"
        );
    }

    /**
     * @Then content :name is of (content) type :contentType
     *
     * Checks if content mapped by 'name' is of content type 'contentType'
     */
    public function thenContentIsOfType( $name, $contentType )
    {
        $content = $this->getContentByNameMap( $name );

        $realContentType = $this->getContentManager()->getContentType( $content, $contentType );
        Assertion::assertEquals(
            $realContentType,
            $contentType,
            "The content '$name' does not have content type '$contentType'"
        );
    }

    /**
     * @Then content :name is not of (content) type :contentType
     * @Then content :name isnt of (content) type :contentType
     *
     * Checks if content mapped by 'name' is not of content type 'contentType'.
     */
    public function thenContentIsNotOfType( $name, $contentType )
    {
        $content = $this->getContentByNameMap( $name );

        $realContentType = $this->getContentManager()->getContentType( $content, $contentType );
        Assertion::assertNotEquals(
            $realContentType,
            $contentType,
            "The content '$name' is of unexpected content type '$contentType'"
        );
    }

    /**
     * @Then content :name has (the) (following) fields:
     * @Then content :name exists with (the) (following) fields:
     *
     * Tests if content mapped by 'name' contains the same field values as the ones provided in given data.
     * (See step/sentence "Given content :name is updated with fields" for example(s). )
     */
    public function thenContentExistsWithFields( $name, PyStringNode $fieldData )
    {
        $content = $this->getContentByNameMap( $name );
        $fields = Gherkin::getMultilineTable( $fieldData );

        Assertion::assertTrue(
            $this->getContentManager()->checkContentFieldsMatch( $content->id, $fields ),
            "Fields for content '$name' did not match expected field data."
        );
    }

    /**
     * @Then content :name is the same as (content) :secondName
     *
     * Checks that contents mapped by different names are the same instance.
     */
    public function thenContentIsTheSameAs( $name, $secondName )
    {
        $content1 = $this->getContentByNameMap( $name );
        $content2 = $this->getContentByNameMap( $secondName );

        Assertion::assertEquals(
            $content1->id,
            $content2->id,
            "Contents '$name' and '$secondName' are not the same."
        );
    }

}
