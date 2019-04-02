<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\ContentData;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\ContentCreateStruct;
use EzSystems\BehatBundle\API\ContentData\FieldTypesData\FieldTypeDataCreator;

class ContentDataCreator
{
    private $contentTypeIdentifier;

    private $contentTypeService;

    private $contentService;

    private $fieldtypeDataCreator;

    public function __construct(ContentTypeService $contentTypeService, ContentService $contentService, FieldTypeDataCreator $fieldTypeDataCreator)
    {
        $this->contentTypeService = $contentTypeService;
        $this->contentService = $contentService;
        $this->fieldtypeDataCreator = $fieldTypeDataCreator;
    }

    public function setContentTypeIdentifier(string $contentTypeIdentifier)
    {
        $this->contentTypeIdentifier = $contentTypeIdentifier;
    }

    public function getRandomContentData($language): ContentCreateStruct
    {
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier($this->contentTypeIdentifier);

        $contentCreateStruct = $this->contentService->newContentCreateStruct($contentType, $language);

        $fieldDefinitions = $contentType->getFieldDefinitions();

        foreach ($fieldDefinitions as $field)
        {
            $contentCreateStruct->setField($field->identifier, $this->fieldtypeDataCreator->getData($field->fieldTypeIdentifier, $language), $language);
        }

        return $contentCreateStruct;
    }

    public function getFilledContentDataStruct($contentItemData, $language): ContentCreateStruct
    {
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier($this->contentTypeIdentifier);
        $contentCreateStruct = $this->contentService->newContentCreateStruct($contentType, $language);

        foreach ($contentItemData as $fieldIdentifier => $value)
        {
            $contentCreateStruct->setField($fieldIdentifier, $value);
        }

        return $contentCreateStruct;
    }
}
