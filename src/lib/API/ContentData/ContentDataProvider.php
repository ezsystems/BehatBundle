<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\ContentData;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\ContentCreateStruct;
use EzSystems\Behat\API\ContentData\FieldTypeData\FieldTypeDataProviderInterface;

class ContentDataProvider
{
    private $contentTypeIdentifier;

    private $contentTypeService;

    private $contentService;

    /** @var  FieldTypeDataProviderInterface[] */
    private $fieldTypeDataProviders;

    public function __construct(ContentTypeService $contentTypeService, ContentService $contentService)
    {
        $this->contentTypeService = $contentTypeService;
        $this->contentService = $contentService;
    }

    public function addFieldTypeDataProvider(FieldTypeDataProviderInterface $fieldTypeDataProvider)
    {
        $this->fieldTypeDataProviders[] = $fieldTypeDataProvider;
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
            $fieldData = $this->getFieldData($field->fieldTypeIdentifier, $language);
            $contentCreateStruct->setField($field->identifier, $fieldData, $language);
        }

        return $contentCreateStruct;
    }

    public function getFilledContentDataStruct(ContentCreateStruct $contentCreateStruct, $contentItemData, $language): ContentCreateStruct
    {
        foreach ($contentItemData as $fieldIdentifier => $value)
        {
            $contentCreateStruct->setField($fieldIdentifier, $value);
        }

        return $contentCreateStruct;
    }

    public function getFieldData($fieldIdentifier, $language)
    {
        foreach ($this->fieldTypeDataProviders as $provider)
        {
            if ($provider->canWork($fieldIdentifier)) {
                return $provider->generateData($language);
            }
        }
    }
}
