<?php
/**
 * This file is part of the BehatBundle package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\ObjectManager;

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
     * Publishes the content
     *
     * @param string The field name
     * @param mixed The field value
     */
    public function createContent( $contentType, $name, $location )
    {
        $repository = $this->getRepository();
        $languageCode = self::DEFAULT_LANGUAGE;

        $content = $repository->sudo(
            function() use( $repository, $languageCode, $contentType, $name, $location )
            {
                $contentService = $repository->getcontentService();
                $contentTypeService = $repository->getContentTypeService();
                $locationCreateStruct = $repository->getLocationService()->newLocationCreateStruct( $location );

                $contentType = $contentTypeService->loadContentTypeByIdentifier( $contentType );
                $contentCreateStruct = $contentService->newContentCreateStruct( $contentType, $languageCode );
                $contentCreateStruct->setField( 'name', $name );

                $draft = $contentService->createContent( $contentCreateStruct, array( $locationCreateStruct ) );
                $content = $contentService->publishVersion( $draft->versionInfo );

                return $content;
            }
        );

        return $content->contentInfo->mainLocationId;
    }

    /**
     * Creates and publishes a content in a given path
     * the container are assumed to be folders
     *
     * @param string $path The content path
     * @param mixed $contentType The content type identifier
     */
    public function createContentWithPath( $path, $contentType )
    {
        $contentsName = explode( '/', $path );
        $location = '2';

        foreach ( $contentsName as $name )
        {
            if ( $name != end( $contentsName ) )
            {
                $location = $this->createContent( 'folder', $name, $location );
            }
        }
        $location = $this->createContent( $contentType, $name, $location );
        $this->mapContentPath( $path );

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
     * NOT USED FOR NOW
     */
    protected function destroy( ValueObject $object )
    {
    // do nothing for now, to be implemented later when decided waht to do with the created objects
    // must be empty because this method allways called from above
    }
}
