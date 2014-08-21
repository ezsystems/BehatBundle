<?php
/**
 * File containing the ContentTypeGroup context class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\ObjectContext;

use EzSystems\BehatBundle\Sentence\ObjectSentence\ContentTypeGroup as ContentTypeGroupObjectSentence;
use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\API\Repository\Exceptions as ApiExceptions;
use eZ\Publish\Core\Base\Exceptions as CoreExceptions;
use Behat\Gherkin\Node\TableNode;
use PHPUnit_Framework_Assert as Assertion;

class ContentTypeGroup extends Base implements ContentTypeGroupObjectSentence
{
    /**
     * Given I have a Content Type Group with identifier "<identifier>"
     */
    public function iHaveContentTypeGroup( $identifier )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $newContentTypeGroup = $repository->sudo(
            function() use( $identifier, $contentTypeService )
            {
                // verify if the content type group exists
                try
                {
                    $group = $contentTypeService->loadContentTypeGroupByIdentifier( $identifier );
                    return $group->id;
                }
                // other wise create it
                catch ( ApiExceptions\NotFoundException $e )
                {
                    $ContentTypeGroupCreateStruct = $contentTypeService->newContentTypeGroupCreateStruct( $identifier );
                    return $contentTypeService->createContentTypeGroup( $ContentTypeGroupCreateStruct );
                }
            }
        );

        if ( !is_int( $newContentTypeGroup ) )
        {
            $this->createdObjects[] = $newContentTypeGroup;
            return $newContentTypeGroup->id;
        }

        return $newContentTypeGroup;
    }

    /**
     * Given I have a Content Type Group with id "<id>
     * Given there is a Content Type Group with id "<id>"
     */
    public function iHaveContentTypeGroupWithId( $id )
    {
        $groupId = $this->iHaveContentTypeGroup( 'ctg' . rand( 10000, 99999 ) );

        $this->getMainContext()->getSubContext( 'Common' )->addValuesToMap( $id, $groupId );
    }

    /**
     * Given I have a Content Type Group with id "<id>" and identifier "<identifier>"
     * Given there is a Content Type Group with id "<id>" and identifier "<identifier>"
     */
    public function iHaveContentTypeGroupWithIdAndIdentifier( $id, $identifier )
    {
        $groupId = $this->iHaveContentTypeGroup( $identifier );

        $this->getMainContext()->getSubContext( 'Common' )->addValuesToMap( $id, $groupId );
    }

    /**
     * Given there is not|isn't a Content Type Group with id "<id>""
     */
    public function iDontHaveContentTypeGroupWithId( $id )
    {
        $randId = rand( 100, 999 );

        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $repository->sudo(
            function() use( $randId, $contentTypeService )
            {
                // attempt to delete the content type group with the identifier
                try
                {
                    $contentTypeService->deleteContentTypeGroup(
                        $contentTypeService->loadContentTypeGroup( $randId )
                    );
                }
                catch ( ApiExceptions\NotFoundException $e )
                {
                    // nothing to do
                }
            }
        );

        $this->getMainContext()->getSubContext( 'Common' )->addValuesToMap( $id, $randId );
    }

    /**
     * Given I do not|don't have a Content Type Group with identifier "<identifier>"
     */
    public function iDonTHaveContentTypeGroup( $identifier )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $repository->sudo(
            function() use( $identifier, $contentTypeService )
            {
                // attempt to delete the content type group with the identifier
                try
                {
                    $contentTypeService->deleteContentTypeGroup(
                        $contentTypeService->loadContentTypeGroupByIdentifier( $identifier )
                    );
                }
                catch ( ApiExceptions\NotFoundException $e )
                {
                    // nothing to do
                }
            }
        );
    }

    /**
     * Given I have the following Content Type Groups:
     */
    public function iHaveTheFollowingContentTypeGroups( TableNode $table )
    {
        $groups = $table->getNumeratedRows();

        array_shift( $groups );
        foreach ( $groups as $group )
        {
            $this->iHaveContentTypeGroup( $group[0] );
        }
    }

    /**
     * Then Content Type Group with identifier "<identifier>" is stored|removed
     */
    public function contentTypeGroupIs( $identifier, $action )
    {
        $this->assertExistenceOfContentTypeGroupByIdentifier( $this->shouldBeFound( $action ), $identifier );
    }

    /**
     * Then Content Type Group with identifier "<identifier>" was not|wasn't stored|removed
     */
    public function contentTypeGroupIsNot( $identifier, $action )
    {
        $this->assertExistenceOfContentTypeGroupByIdentifier( $this->shouldBeFound( $action, true ), $identifier );
    }

    /**
     * Then only <total> Content Type Group with identifier "<identifier>" is stored$/
     */
    public function countContentTypeGroup( $total, $identifier )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getMainContext()->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        // get all content type groups
        $contentTypeGroupList = $repository->sudo(
            function() use ( $contentTypeService )
            {
                return $contentTypeService->loadContentTypeGroups();
            }
        );

        // count how many are found with $identifier
        $count = 0;
        foreach ( $contentTypeGroupList as $contentTypeGroup )
        {
            if ( $contentTypeGroup->identifier === $identifier )
            {
                $count++;
            }
        }

        Assertion::assertEquals(
            $total,
            $count,
            "Expected '$total' ContentTypeGroups with '$identifier' identifier but found '$count'"
        );
    }

    /**
     * Asserts that Content Type Group exists (or not)
     *
     * @param $find
     * @param $identifier
     */
    protected function assertExistenceOfContentTypeGroupByIdentifier( $find, $identifier )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $found = $repository->sudo(
            function() use( $identifier, $contentTypeService )
            {
                try
                {
                    $contentTypeService->loadContentTypeGroupByIdentifier( $identifier );

                    // if no exception was thrown is because it was found
                    return true;
                }
                // other wise return false
                catch ( ApiExceptions\NotFoundException $e )
                {
                    return false;
                }
            }
        );

        // do assertions
        if ( $find )
        {
            Assertion::assertTrue(
                $found,
                "ContentTypeGroup with identifier {$identifier} wasn't found/saved"
            );
        }
        else
        {
            Assertion::assertFalse(
                $found,
                "ContentTypeGroup with identifier {$identifier} was unexpectedly found"
            );
        }
    }

    /**
     * This is used by the __destruct() function to delete/remove all the objects
     * that were created for testing
     *
     * @param \eZ\Publish\API\Repository\Values\ValueObject $object Object that should be destroyed/removed
     */
    protected function destroy( ValueObject $object )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();

        $repository->sudo(
            function() use( $repository, $object )
            {
                $contentTypeService = $repository->getContentTypeService();
                try
                {
                    $contentTypeService->deleteContentTypeGroup( $contentTypeService->loadContentTypeGroup( $object->id ) );
                }
                // if there it have Content Type's, then remove them
                catch ( ApiExceptions\InvalidArgumentException $e )
                {
                    $contentTypeList = $contentTypeService->loadContentTypes( $object );
                    foreach ( $contentTypeList as $contentType )
                    {
                        $contentTypeService->deleteContentType( $contentType );
                    }

                    $contentTypeService->deleteContentTypeGroup( $contentTypeService->loadContentTypeGroup( $object->id ) );
                }
                catch ( ApiExceptions\NotFoundException $e )
                {
                    // nothing to do
                }
            }
        );
    }
}
