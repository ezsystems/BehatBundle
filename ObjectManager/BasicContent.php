<?php
/**
 * This file is part of the BehatBundle package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\ObjectManager;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\API\Repository\Exceptions as ApiExceptions;
use Behat\Symfony2Extension\Context\KernelAwareContext;

class BasicContent extends Base
{
    /**
     * Default language
     */
    const DEFAULT_LANGUAGE = 'eng-GB';

    /**
     * Content path mapping
     */
    private $contentPaths = array();

    /**
     * Creates and publishes a Content.
     *
     * @param string $contentType
     * @param array $fields
     * @param mixxed $parentLocationId
     *
     * @return mixed The content's main location id
     */
    public function createContent( $contentType, $fields, $parentLocationId )
    {
        $repository = $this->getRepository();
        $languageCode = self::DEFAULT_LANGUAGE;

        $content = $this->getRepository()->sudo(
            function(Repository $repository) use ( $parentLocationId, $contentType, $fields, $languageCode )
            {
                $content = $this->createContentDraft($parentLocationId, $contentType, $fields, $languageCode);
                return $content = $repository->getContentService()->publishVersion($content->versionInfo);
            }
        );

        return $content->contentInfo->mainLocationId;
    }

    /**
     * Publishes a content draft.
     *
     * @param Content $content
     */
    public function publishDraft( Content $content )
    {
        $this->getRepository()->sudo(
            function(Repository $repository) use ( $content )
            {
                $repository->getContentService()->publishVersion($content->versionInfo->id);
            }
        );
    }

    /**
     * Creates a content draft using sudo().
     *
     * @param Location $parentLocationId
     * @param string $contentTypeIdentifier
     * @param string $languageCode
     * @param array $fields Fields, as primitives understood by setField
     *
     *@return Content an unpublished Content draft
     */
    function createContentDraft($parentLocationId, $contentTypeIdentifier, $fields, $languageCode = null )
    {
        $languageCode = $languageCode ?: self::DEFAULT_LANGUAGE;

        $repository = $this->getRepository();
        $content = $repository->sudo(
            function() use( $repository, $languageCode, $contentTypeIdentifier, $fields, $parentLocationId )
            {
                $contentService = $repository->getcontentService();
                $contentTypeService = $repository->getContentTypeService();
                $locationCreateStruct = $repository->getLocationService()->newLocationCreateStruct( $parentLocationId );

                $contentTypeIdentifier = $contentTypeService->loadContentTypeByIdentifier( $contentTypeIdentifier );
                $contentCreateStruct = $contentService->newContentCreateStruct( $contentTypeIdentifier, $languageCode );
                foreach ( array_keys( $fields ) as $key ) {
                    $contentCreateStruct->setField( $key, $fields[$key] );
                }
                return $contentService->createContent( $contentCreateStruct, array( $locationCreateStruct ) );
            }
        );

        $this->addObjectToList( $content );

        return $content;
    }

    /**
     * Creates and publishes a content at a given path.
     * Non-existing path items are created as folders named after the path element.
     *
     * @param string $path The content path
     * @param array $fields
     * @param mixed $contentType The content type identifier
     *
     * @return mixed|string
     */
    public function createContentWithPath( $path, $fields, $contentType )
    {
        $contentsName = explode( '/', $path );
        $currentPath = '';
        $location = '2';

        foreach ( $contentsName as $name )
        {
            if ( $name != end( $contentsName ) )
            {
                $location = $this->createContent( 'folder', [ 'name' => $name ], $location );
            }
            if ( $currentPath != '' )
            {
                $currentPath .= '/';
            }
            $currentPath .=  $name;
            $this->mapContentPath( $currentPath );
        }
        $location = $this->createContent( $contentType, $fields, $location );

        return $location;
    }

    /**
     * Getter for $contentPaths
     */
    public function getContentPath( $name )
    {
        return $this->contentPaths[$name];
    }

    /**
     * Maps the path of the content to it's name for later use
     */
    private function mapContentPath( $path )
    {
        $contentNames = explode( '/', $path );
        $this->contentPaths[ end( $contentNames ) ] = $path;
    }

    /**
     * Deletes the content object provided
     * used to clean after testing
     */
    protected function destroy( ValueObject $object )
    {
         /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $contentId = $object->id;
        $repository->sudo(
            function() use( $repository, $contentId )
            {
                try
                {
                    $contentService = $repository->getContentService();
                    $contentInfo = $contentService->loadContentInfo( $contentId );
                    $contentService->deleteContent( $contentInfo );
                }
                catch ( ApiExceptions\NotFoundException $e )
                {
                    // Nothing to do
                }
            }
        );
    }
}
