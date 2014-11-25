<?php
/**
 * File containing the ContentTypeGroup context
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Context\Object;

use Behat\Gherkin\Node\TableNode;
use PHPUnit_Framework_Assert as Assertion;

/**
 * Sentences for ContentTypeGroups
 */
trait ContentTypeGroup
{
    /**
     * @Given there is a Content Type Group with identifier :identifier
     *
     * Ensures a content type group exists, creating a new one if it doesn't.
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup
     */
    public function ensureContentTypeGroupExists( $identifier )
    {
        return $this->getContentTypeGroupManager()->ensureContentTypeGroupExists( $identifier );
    }

    /**
     * @Given there isn't a Content Type Group with identifier :identifier
     *
     * Ensures a content type group does not exist, removing it if necessary.
     */
    public function ensureContentTypeGroupDoesntExist( $identifier )
    {
        $this->getContentTypeGroupManager()->ensureContentTypeGroupDoesntExist( $identifier );
    }

    /**
     * @Given there is Content Type Group with id :id
     *
     * Creates a new content type group with a non-existent identifier, and maps it's id to ':id'
     */
    public function ensureContentTypeGroupWithIdExists( $id )
    {
        $identifier = $this->findNonExistingContentTypeGroupIdentifier();

        $this->ensureContentTypeGroupWithIdAndIdentifierExists( $id, $identifier );
    }

    /**
     * @Given there isn't a Content Type Group with id :id
     *
     * Maps id ':id' to a non-existent content type group.
     */
    public function ensureContentTypeGroupWithIdDoesntExist( $id )
    {
        $randomId = $this->findNonExistingContentTypeGroupId();

        $this->addValuesToKeyMap( $id, $randomId );
    }

    /**
     * @Given there is a Content Type Group with id :id and identifier :identifier
     *
     * nsures a content type group exists with identifier ':identifier', and maps it's id to ':id'
     */
    public function ensureContentTypeGroupWithIdAndIdentifierExists( $id, $identifier )
    {
        $contentTypeGroup = $this->ensureContentTypeGroupExists( $identifier );

        $this->addValuesToKeyMap( $id, $contentTypeGroup->id );
    }

    /**
     * @Given there are the following Content Type Groups:
     *
     * Make sure that content type groups in the provided table exist, by identifier. Example:
     *      | group                 |
     *      | testContentTypeGroup1 |
     *      | testContentTypeGroup2 |
     *      | testContentTypeGroup3 |
     */
    public function ensureContentTypeGroupsExists( TableNode $table )
    {
        $contentTypeGroups = $table->getTable();

        array_shift( $contentTypeGroups );
        foreach ( $contentTypeGroups as $contentTypeGroup )
        {
            $this->ensureContentTypeGroupExists( $contentTypeGroup[0] );
        }
    }

    /**
     * @Then Content Type Group with id :id exists
     *
     * Checks that content type group with (mapped) id ':id' exists
     */
    public function assertContentTypeGroupExists( $id )
    {
        $realId = $this->getValuesFromKeyMap( $id );
        Assertion::assertTrue(
            $this->getContentTypeGroupManager()->checkContentTypeGroupExistence( $realId ),
            "Couldn't find ContentTypeGroup with id $id"
        );
    }

    /**
     * @Then Content Type Group with identifier :identifier exists
     * @Then Content Type Group with identifier :identifier was created
     * @Then Content Type Group with identifier :identifier wasn't deleted
     *
     * Checks that content type group with identifier ':identifier' exists
     */
    public function assertContentTypeGroupWithIdentifierExists( $identifier )
    {
        Assertion::assertTrue(
            $this->getContentTypeGroupManager()->checkContentTypeGroupExistenceByIdentifier( $identifier ),
            "Couldn't find ContentTypeGroup with identifier '$identifier'"
        );
    }

    /**
     * @Then Content Type Group with id :id doesn't exist
     *
     *  Checks that content type group with (mapped) id ':id' does not exist
     */
    public function assertContentTypeGroupDoesntExist( $id )
    {
        $realId = $this->getValuesFromKeyMap( $id );
        Assertion::assertFalse(
            $this->getContentTypeGroupManager()->checkContentTypeGroupExistence( $realId ),
            "Unexpected ContentTypeGroup with id $id found."
        );
    }

    /**
     * @Then Content Type Group with identifier :identifier doesn't exist (anymore)
     * @Then Content Type Group with identifier :identifier wasn't created
     * @Then Content Type Group with identifier :identifier was deleted
     *
     * Checks that content type group with identifier ':identifier' does not exist
     */
    public function assertContentTypeGroupWithIdentifierDoesntExist( $identifier )
    {
        Assertion::assertFalse(
            $this->getContentTypeGroupManager()->checkContentTypeGroupExistenceByIdentifier( $identifier ),
            "Unexpected ContentTypeGroup with identifer '$identifier' found"
        );
    }

    /**
     * @Then (only) :total Content Type Group(s) with identifier :identifier exists
     * @Then (only) :total Content Type Group(s) exists with identifier :identifier
     *
     * Checks that there are exactly ':total' content type groups with identifier ':identifier'
     */
    public function assertTotalContentTypeGroups( $total, $identifier )
    {
        Assertion::assertEquals(
            $this->getContentTypeGroupManager()->countContentTypeGroup( $identifier ),
            $total
        );
    }

    /**
     * Find an non existent ContentTypeGroup ID
     *
     * @return int Non existing ID
     *
     * @throws \Exception Possible endless loop
     */
    private function findNonExistingContentTypeGroupId()
    {
        $i = 0;
        while ( $i++ < 20 )
        {
            $id = rand( 1000, 9999 );
            if ( ! $this->getContentTypeGroupManager()->checkContentTypeGroupExistence( $id ) )
            {
                return $id;
            }
        }

        throw new \Exception( 'Possible endless loop when attempting to find a new identifier to ContentTypeGroups' );
    }

    /**
     * Find a non existing ContentTypeGroup identifier
     *
     * @return string A not used identifier
     *
     * @throws \Exception Possible endless loop
     */
    private function findNonExistingContentTypeGroupIdentifier()
    {
        $i = 0;
        while ( $i++ < 20 )
        {
            $identifier = 'ctg' . rand( 10000, 99999 );
            if ( ! $this->getContentTypeGroupManager()->checkContentTypeGroupExistenceByIdentifier( $identifier ) )
            {
                return $identifier;
            }
        }

        throw new \Exception( 'Possible endless loop when attempting to find a new identifier to ContentTypeGroups' );
    }
}
