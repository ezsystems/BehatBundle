<?php
/**
 * File containing the ContentTypeGroup context class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\ObjectManager;

use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\API\Repository\Exceptions as ApiExceptions;
use eZ\Publish\Core\Base\Exceptions as CoreExceptions;
use Behat\Gherkin\Node\TableNode;
use Symfony\Component\HttpKernel\KernelInterface;

class ContentTypeGroup extends Base
{
    /**
     * Ensure ContentTypeGroup exists
     *
     * @param string $identifier
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup
     */
    public function ensureContentTypeGroupExists( $identifier )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $result = $repository->sudo(
            function() use( $identifier, $contentTypeService )
            {
                $found = false;
                // verify if the content type group exists
                try
                {
                    $contentTypeGroup = $contentTypeService->loadContentTypeGroupByIdentifier( $identifier );
                    $found = true;
                }
                // other wise create it
                catch ( ApiExceptions\NotFoundException $e )
                {
                    $ContentTypeGroupCreateStruct = $contentTypeService->newContentTypeGroupCreateStruct( $identifier );
                    $contentTypeGroup = $contentTypeService->createContentTypeGroup( $ContentTypeGroupCreateStruct );
                }

                return array(
                    'found'             => $found,
                    'contentTypeGroup'  => $contentTypeGroup
                );
            }
        );

        if ( !$result['found'] )
        {
            $this->addObjectToList( $result['contentTypeGroup'] );
        }

        return $result['contentTypeGroup'];
    }

    /**
     * Checks if the ContentTypeGroup with $id exists
     *
     * @param string $id Identifier of the possible content
     *
     * @return boolean True if it exists
     */
    public function checkContentTypeGroupExistence( $id )
    {
       /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        return $repository->sudo(
            function() use( $id, $contentTypeService )
            {
                // attempt to load the content type group with the id
                try
                {
                    $contentTypeService->loadContentTypeGroup( $id );
                    return true;
                }
                catch ( ApiExceptions\NotFoundException $e )
                {
                    return false;
                }
            }
        );
    }

    /**
     * Checks if the ContentTypeGroup with $identifier exists
     *
     * @param string $identifier Identifier of the possible content
     *
     * @return boolean True if it exists
     */
    public function checkContentTypeGroupExistenceByIdentifier( $identifier )
    {
       /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        return $repository->sudo(
            function() use( $identifier, $contentTypeService )
            {
                // attempt to load the content type group with the identifier
                try
                {
                    $contentTypeService->loadContentTypeGroupByIdentifier( $identifier );
                    return true;
                }
                catch ( ApiExceptions\NotFoundException $e )
                {
                    return false;
                }
            }
        );
    }

    /**
     * Ensure that no ContentTypeGroup with $identifier exists
     *
     * @param string $identifier Identifier of the ContentTypeGroup
     */
    public function ensureContentTypeGroupDoesntExist( $identifier )
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
     * Count total ContentTypeGroups with $identifier
     *
     * @param string $identifier Identifier of the ContentTypeGroup
     *
     * @return int Total ContentTypeGroups with $identifier found
     */
    public function countContentTypeGroup( $identifier )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
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

        return $count;
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
