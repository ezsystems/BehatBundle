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
use Behat\Symfony2Extension\Context\KernelAwareContext;

class FieldType extends Base
{
    /**
     * Defines the state of the Construction object, if it's not published, partialy or completely published
     */
    const NO_FIELD_CREATED = -1;
    const FIELD_CREATED = 0;
    const FIELD_NOT_ASSOCIATED = 1;
    const FIELD_ASSOCIATED = 2;
    const CONTENT_TYPE_PUBLISHED = 3;
    const CONTENT_PUBLISHED = 4;

    /**
     * @var stores the values needed to build the contentType with the desired fieldTypes, used to postpone until object is ready for publishing
     */
    private $fieldConstructionObject = array(
        "contentType" => null,
        "fieldType" => null,
        "content" => null,
        "objectState" => self::NO_FIELD_CREATED
    );

    /**
     * @var stores internal mapping of the fieldType names
     */
    private $fieldTypeInternalIdentifier = array(
        "integer" => "ezinteger"
    );

    /**
     *
     * @param KernelAwareContext $context
     */
    public function __construct( KernelAwareContext $context )
    {
        $this->priority = 1;    //TODOI
        parent::__construct( $context );
    }

    /**
     * DOC TO BE DONE
     *
     * @param    string  $fieldType
     */
    public function createField( $fieldType, $name = null )
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();
        $fieldPosition = $this->getActualFieldPosition();
        $name = ( $name == null ? $fieldType : $name );
        $fieldCreateStruct = $contentTypeService->newFieldDefinitionCreateStruct(
            $name,
            $this->fieldTypeInternalIdentifier[ $fieldType ]
        );
        $fieldCreateStruct->names = array( self::DEFAULT_LANGUAGE => $name );
        $fieldCreateStruct->position = $fieldPosition;
        $this->fieldConstructionObject[ 'fieldType' ] = $fieldCreateStruct;
        $this->fieldConstructionObject[ 'objectState' ] = self::FIELD_CREATED;
    }

    public function executeDelayedOperations()
    {
        if ( $this->fieldConstructionObject[ 'objectState' ] == self::NO_FIELD_CREATED )
        {
            throw new \Exception( 'A field type must be declared before anything else' );
        }
        if ( $this->fieldConstructionObject[ 'objectState' ] == self::FIELD_CREATED )
        {
            $name = $this->fieldConstructionObject[ 'fieldType' ]->identifier;
            $name .= "#" . rand( 1000, 9000 );
            $this->createContentType( $name );
        }
        if ( $this->fieldConstructionObject[ 'objectState' ] == self::FIELD_NOT_ASSOCIATED )
        {
            $this->associateFieldToCotentType();
        }
        if ( $this->fieldConstructionObject[ 'objectState' ] == self::FIELD_ASSOCIATED )
        {
            $this->publishContentType();
        }
        if ( $this->fieldConstructionObject[ 'objectState' ] == self::CONTENT_TYPE_PUBLISHED )
        {
            $this->publishContent();
        }
    }

    private function publishContent()
    {
        $repository = $this->getRepository();
        $languageCode = self::DEFAULT_LANGUAGE;
        $content = $repository->sudo(
            function() use( $repository, $languageCode )
            {
                $contentService = $repository->getcontentService();
                $locationCreateStruct = $repository->getLocationService()->newLocationCreateStruct( '2' );
                $contentType = $this->fieldConstructionObject[ 'contentType' ];
                $contentCreateStruct = $contentService->newContentCreateStruct( $contentType, $languageCode );
                $draft = $contentService->createContent( $contentCreateStruct, array( $locationCreateStruct ) );
                $content = $contentService->publishVersion( $draft->versionInfo );

                return $content;
            }
        );
        $this->fieldConstructionObject[ 'content' ] = $content;
        $this->fieldConstructionObject[ 'objectState' ] = self::CONTENT_PUBLISHED;
    }

    private function associateFieldToCotentType()
    {
        $fieldCreateStruct = $this->fieldConstructionObject[ 'fieldType' ];
        $this->fieldConstructionObject[ 'contentType' ]->addFieldDefinition( $fieldCreateStruct );
        $this->fieldConstructionObject[ 'objectState' ] = self::FIELD_ASSOCIATED;
    }

    private function publishContentType()
    {
        $repository = $this->getRepository();
        $repository->sudo(
            function() use( $repository )
            {
                $contentTypeService = $repository->getContentTypeService();
                $contentTypeGroup = $contentTypeService->loadContentTypeGroupByIdentifier( 'Content' );
                $contentTypeCreateStruct = $this->fieldConstructionObject[ 'contentType' ];
                $contentTypeDraft = $contentTypeService->createContentType( $contentTypeCreateStruct, array( $contentTypeGroup ) );
                $contentTypeService->publishContentTypeDraft( $contentTypeDraft );
            }
        );
        $contentTypeIdentifier = $this->fieldConstructionObject[ 'contentType' ]->identifier;
        $contentType = $repository->getContentTypeService()->loadContentTypeByIdentifier( $contentTypeIdentifier );
        $this->fieldConstructionObject[ 'contentType' ] = $contentType;
        $this->fieldConstructionObject[ 'objectState' ] = self::CONTENT_TYPE_PUBLISHED;
    }

    /**
     * DOC TO BE DONE
     *
     * @param   string  $fieldType
     */
    private function createContentType( $name )
    {
        $repository = $this->getRepository();

        $contentTypeService = $repository->getContentTypeService();
        $identifier = strtolower( $name );
        $contentTypeCreateStruct = $contentTypeService->newContentTypeCreateStruct( $identifier );
        $contentTypeCreateStruct->mainLanguageCode = self::DEFAULT_LANGUAGE;
        $contentTypeCreateStruct->names = array( self::DEFAULT_LANGUAGE => $name );
        $this->fieldConstructionObject[ 'contentType' ] = $contentTypeCreateStruct;
        $this->fieldConstructionObject[ 'objectState' ] = self::FIELD_NOT_ASSOCIATED;
    }

    private function getActualFieldPosition()
    {
        if ( $this->fieldConstructionObject[ 'fieldType' ] == null )
        {
            return 10;
        }
        else
        {
            return $this->fieldConstructionObject[ 'fieldType' ]->position + 10;
        }
    }

    protected function destroy( ValueObject $object )
    {
    }
}
