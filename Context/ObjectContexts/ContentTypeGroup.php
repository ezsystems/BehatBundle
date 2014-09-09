<?php

namespace EzSystems\BehatBundle\Context\ObjectContexts;

use EzSystems\BehatBundle\Helpers\KeyMapping;
use Behat\Gherkin\Node\TableNode;
use PHPUnit_Framework_Assert as Assertion;

trait ContentTypeGroup
{
    /**
     * @Given there is a Content Type Group with identifier :identifier
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup
     */
    public function assureContentTypeGroupExist( $identifier )
    {
        return $this->getContentTypeGroupManager()->assureContentTypeGroupExist( $identifier );
    }

    /**
     * @Given there isn't a Content Type Group with identifier :identifier
     */
    public function assureContentTypeGroupDontExist( $identifier )
    {
        $this->getContentTypeGroupManager()->assureContentTypeGroupDoesntExist( $identifier );
    }

    /**
     * @Given there is Content Type Group with id :id
     */
    public function assureContentTypeGroupWithIdExist( $id )
    {
        $identifier = $this->findNonExistingContentTypeGroupIdentifier();

        $this->assureContentTypeGroupWithIdAndIdentifierExist( $id, $identifier );
    }

    /**
     * @Given there isn't a Content Type Group with id :id
     */
    public function assureContentTypeGroupWithIdDontExist( $id )
    {
        $randomId = $this->findNonExistingContentTypeGroupId();

        $this->addValuesToKeyMap( $id, $randomId );
    }

    /**
     * @Given there is a Content Type Group with id :id and identifier :identifier
     */
    public function assureContentTypeGroupWithIdAndIdentifierExist( $id, $identifier )
    {
        $contentTypeGroup = $this->assureContentTypeGroupExist( $identifier );

        $this->addValuesToKeyMap( $id, $contentTypeGroup->id );
    }

    /**
     * @Given there are the following Content Type Groups:
     */
    public function assureContentTypeGroupsExist( TableNode $table )
    {
        $contentTypeGroups = $table->getTable();

        array_shift( $contentTypeGroups );
        foreach ( $contentTypeGroups as $contentTypeGroup )
        {
            $this->assureContentTypeGroupExist( $contentTypeGroup[0] );
        }
    }

    /**
     * @Then Content Type Group with identifier :identifier exists
     * @Then Content Type Group with identifier :identifier was created
     * @Then Content Type Group with identifier :identifier wasn't deleted
     */
    public function assertContentTypeGroupWithIdentifierExist( $identifier )
    {
        Assertion::assertTrue(
            $this->getContentTypeGroupManager()->checkContentTypeGroupExistenceByIdentifier( $identifier ),
            "Couldn't find ContentTypeGroup with identifier '$identifier'"
        );
    }

    /**
     * @Then Content Type Group with identifier :identifier doesn't exists (anymore)
     * @Then Content Type Group with identifier :identifier wasn't created
     * @Then Content Type Group with identifier :identifier was deleted
     */
    public function assertContentTypeGroupWithIdentifierDoesntExist( $identifier )
    {
        Assertion::assertFalse(
            $this->getContentTypeGroupManager()->checkContentTypeGroupExistenceByIdentifier( $identifier ),
            "Unexpected ContentTypeGroup with identifer '$identifier' found"
        );
    }

    /**
     * @Then only :total Content Type Group(s) with identifier :identifier exists
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
