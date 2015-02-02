<?php
/**
 * File containing the User ObjectManager class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\ObjectManager;

use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use eZ\Publish\API\Repository\Exceptions as ApiExceptions;


class Content extends Base
{
    const DEFAULT_LANGUAGE = 'eng-GB';
    /**
     * Load a content by it's id
     *
     * @param  int $contentId            Content id
     * @param  boolean $throwIfNotFound  by default, throws an exception if content is not found.
     *
     * @return eZ\Publish\API\Repository\Values\Content\Content
     */
    public function loadContent( $contentId, $throwIfNotFound = true )
    {
        $repository = $this->getRepository();

        $content = $repository->sudo(
            function() use( $repository, $contentId )
            {
                $contentService = $repository->getContentService();
                try
                {
                    $content = $contentService->loadContent( $contentId );
                }
                catch ( ApiExceptions\NotFoundException $e )
                {
                    $content = false;
                }
                return $content;
            }
        );

        if ( !$content && $throwIfNotFound )
        {
            throw new \Exception( "Could not load content with id '${contentId}'" );
        }

        return $content;
    }


    /**
     * Load a contentInfo by it's content id
     *
     * @param  int $contentId            Content id
     * @param  boolean $throwIfNotFound  by default, throws an exception if content is not found.
     *
     * @return eZ\Publish\API\Repository\Values\Content\ContentInfo
     */
    public function loadContentInfo( $contentId, $throwIfNotFound = true )
    {
        $repository = $this->getRepository();

        $contentInfo = $repository->sudo(
            function() use( $repository, $contentId )
            {
                $contentService = $repository->getContentService();
                try
                {
                    $contentInfo = $contentService->loadContentInfo( $contentId );
                }
                catch ( ApiExceptions\NotFoundException $e )
                {
                    $contentInfo = false;
                }
                return $contentInfo;
            }
        );

        if ( !$contentInfo && $throwIfNotFound )
        {
            throw new \Exception( "Could not load content with id '${contentId}'" );
        }

        return $contentInfo;
    }

    /**
     * Load Location by it's id
     *
     * @param  int $locationId
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     */
    private function loadLocation( $locationId )
    {
        $repository = $this->getRepository();

        $location = $repository->sudo(
            function() use( $repository, $locationId )
            {
                $locationService = $repository->getLocationService();
                return $locationService->loadLocation( $locationId );
            }
        );

        return $location;
    }

    /**
     * Returns the content type name for a given content
     *
     * @param int|eZ\Publish\API\Repository\Values\Content $content
     *
     * @return string  content type name
     */
    public function getContentType( $content )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();

        if ( is_numeric( $content ) )
        {
            $content = $this->loadContent( $content );
        }

        $contentTypeId = $content->contentInfo->contentTypeId;

        $contentType = $repository->sudo(
            function() use( $repository, $contentTypeId )
            {
                $contentTypeService = $repository->getContentTypeService();
                $contentType = $contentTypeService->loadContentType( $contentTypeId );
                return $contentType;
            }
        );

        $contentTypeName = $contentType->getName( self::DEFAULT_LANGUAGE );
        return $contentTypeName;
    }

    /**
     * Load the location id for a content by it's path string, returns null if one is not found.
     *
     * @param  string $locationPath  The location path (uri)
     * @param  bool   $asObject      Optionally return the full location object, not it's id.
     *
     * @return int|eZ\Publish\API\Repository\Values\Content\Location|null
     */
    public function loadLocationByPathString( $locationPath, $asObject = false )
    {
        $repository = $this->getRepository();
        $urlAliasService = $repository->getUrlAliasService();
        try
        {
            $alias = $urlAliasService->lookup( $locationPath );
        }
        catch ( ApiExceptions\NotFoundException $e )
        {
            return false;
        }

        if ( is_numeric( $alias->destination ) )
        {
            $locationId = (int)$alias->destination;
        }
        else
        {
            return null;
        }

        if ( $asObject )
        {
            return $this->loadLocation( $locationId );
        }

        return $locationId;
    }

    /**
     * Load a location id for a given content id
     *
     * @param  int $contentId    The content id, or content object
     * @param  bool $asObject    Optionally return the full location object, not it's id.
     *
     * @return int|\eZ\Publish\API\Repository\Values\Content\Location|null
     */
    public function loadLocationByContentId( $contentId, $asObject = false )
    {
        $content = $this->loadContent( $contentId );
        $mainLocationId = $content->contentInfo->mainLocationId;

        if ( $asObject )
        {
            return $this->loadLocation( $mainLocationId );
        }

        return $mainLocationId;
    }

    /**
     * Create and publish new content of a given content type, with the provided field data.
     *
     * @param  string  $contentTypeIdentifier content type identifier
     * @param  array   $fields                array of fieldName => fieldValue pairs
     * @param  integer $parentLocationId      parent location Id
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    public function createAndPublishContent( $contentTypeIdentifier, $fields, $parentLocationId = 2 )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();

        $languageCode = self::DEFAULT_LANGUAGE;

        $content = $repository->sudo(
            function() use( $repository, $contentTypeIdentifier, $fields, $parentLocationId, $languageCode )
            {
                $contentService = $repository->getcontentService();

                $locationCreateStruct = $repository->getLocationService()->newLocationCreateStruct( $parentLocationId );
                $contentType = $repository->getContentTypeService()->loadContentTypeByIdentifier( $contentTypeIdentifier );

                $contentCreateStruct = $contentService->newContentCreateStruct( $contentType, $languageCode );

                foreach ( $fields as $fieldName => $fieldValue )
                {
                    $fieldDefinition = $contentCreateStruct->contentType->getFieldDefinition( $fieldName );
                    $contentCreateStruct->setField( $fieldName, $fieldValue );
                }

                $draft = $contentService->createContent( $contentCreateStruct, array( $locationCreateStruct ) );
                $content = $contentService->publishVersion( $draft->versionInfo );

                return $content;
            }
        );

        $this->addObjectToList( $content );

        return $content;
    }

    /**
     * Update a content with the provided fields.
     *
     * @param  int $contentId  content id
     * @param  array $fields   indexed array of fields to update
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    public function updateContent( $contentId, $fields )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();

        $languageCode = self::DEFAULT_LANGUAGE;

        $content = $repository->sudo(
            function() use( $repository, $contentId, $fields, $languageCode )
            {
                $contentService = $repository->getcontentService();

                // load content info, create new draft
                $contentInfo = $contentService->loadContentInfo( $contentId );
                $contentDraft = $contentService->createContentDraft( $contentInfo );

                // create and populate a content update struct
                $contentUpdateStruct = $contentService->newContentUpdateStruct();
                $contentUpdateStruct->initialLanguageCode = $languageCode;
                foreach ( $fields as $fieldName => $fieldValue )
                {
                    $contentUpdateStruct->setField( $fieldName, $fieldValue );
                }

                // update content with new draft
                $contentDraft = $contentService->updateContent( $contentDraft->versionInfo, $contentUpdateStruct );

                // publish new version
                $content = $contentService->publishVersion( $contentDraft->versionInfo );
            }
        );
        return $content;
    }

    /**
     * Remove a content by its content id
     *
     * @param  int $contentId  content id
     */
    public function removeContent( $contentId )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();

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

    /**
     * Remove a content by its location id
     *
     * @param  int $locationId  the location id for a content
     */
    public function removeContentWithLocationId( $locationId )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();

        $repository->sudo(
            function() use( $repository, $locationId )
            {
                try
                {
                    $locationService = $repository->getLocationService();
                    $contentService = $repository->getContentService();

                    $location = $locationService->loadLocation( $locationId );
                    $contentService->deleteContent( $location->contentInfo );
                }
                catch ( ApiExceptions\NotFoundException $e )
                {
                    // Nothing to do
                }
            }
        );
    }

    /**
     * Load a content by its location id
     *
     * @param  int $locationId  the location id for a content
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    public function loadContentWithLocationId( $locationId )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();

        $content = $repository->sudo(
            function() use( $repository, $locationId )
            {
                    $locationService = $repository->getLocationService();
                    $contentService = $repository->getContentService();
                    $location = $locationService->loadLocation( $locationId );

                    return $contentService->loadContentByContentInfo( $location->contentInfo );
            }
        );
        return $content;
    }

    /**
     * Destroys/removes from DB the given object
     *
     * @param  ValueObject $object object to destroy.
     */
    protected function destroy( ValueObject $object )
    {
        $this->removeContent( $object->id );
    }

    /**
     * Ensures a content exists matching the provided data, by first performing a search with the
     * ParentLocation and ContentType criterions, then matching all the content fields.
     * If a content is not found, a new one is created using the same parameters.
     *
     * @param  string  $contentTypeIdentifier content type identifier string
     * @param  array   $fields                an indexed array of fields to match
     * @param  integer $parentLocationId      parent location id to search at
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    public function ensureContentExistsWithFields( $contentTypeIdentifier, $fields, $parentLocationId = 2 )
    {
        $repository = $this->getRepository();
        $searchService = $repository->getSearchService();

        $query = new Query();
        $query->filter = new Criterion\LogicalAnd(
            array(
                new Criterion\ParentLocationId( $parentLocationId ),
                new Criterion\ContentTypeIdentifier( $contentTypeIdentifier ),
            )
        );

        $result = $repository->sudo(
            function() use( $query, $searchService )
            {
                return $searchService->findContent( $query, array(), false );
            }
        );

        foreach ( $result->searchHits as $searchHit )
        {
            $content = $searchHit->valueObject;
            if ( $this->checkContentFieldsMatch( $content->id, $fields ) )
            {
                return $content;
            }
        }

        // no content found with matching fields, create a new one.
        return $this->createAndPublishContent( $contentTypeIdentifier, $fields, $parentLocationId );
    }

    /**
     * Checks if a content with the given id exists
     *
     * @param  int $contentId content id
     *
     * @return bool           true if content exists, false if not.
     */
    public function checkContentExists( $contentId )
    {
        $contentInfo = $this->loadContentInfo( $contentId, false );
        return $contentInfo !== false;
    }

    /**
     * Checks if content exists under the wanted location.
     *
     * @param  int $contentId           content id
     * @param  int $parentLocationid    parent location id
     *
     * @return bool                     true if it exists, false if not.
     */
    public function checkContentExistsAtLocation( $contentId, $parentLocationid )
    {
        $content = $this->loadContent( $contentId );
        $location = $this->loadLocation( $content->contentInfo->mainLocationId, true );

        $exists = ( $location->parentLocationId == $parentLocationid );
        return $exists;
    }

    /**
     * Checks if a content exists with the given location description]
     *
     * @param  int $contentId    content id
     * @param  int $locationId   location id
     *
     * @return bool              true if the content exists with the given location, false if not.
     */
    public function checkContentExistsWithLocation( $contentId, $locationId )
    {
        $content = $this->loadContent( $contentId );
        $location = $this->loadLocation( $content->contentInfo->mainLocationId, true );

        $exists = ( $location->id == $locationId );
        return $exists;
    }

    /**
     * Checks that fields in content have the same values as the ones provided.
     *
     * @param  int $contentId    content id
     * @param  array $fieldsData indexed array of field=>values
     *
     * @return bool              true if the fields are the same (string), false if not.
     */
    public function checkContentFieldsMatch( $contentId, $fieldsData )
    {
        $content = $this->loadContent( $contentId );

        foreach ( $fieldsData as $fieldName => $expected )
        {
            $fieldValue = $content->getFieldValue( $fieldName );
            $fieldValue = trim( (string)$fieldValue, "\n" );

            if ( $fieldValue != $expected )
            {
                return false;
            }
        }
        return true;
    }


}
