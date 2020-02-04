<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\ContentData;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\ContentCreateStruct;
use eZ\Publish\API\Repository\Values\Content\ContentStruct;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use EzSystems\BehatBundle\API\ContentData\FieldTypeData\FieldTypeDataProviderInterface;

class ContentDataProvider
{
    private $contentTypeIdentifier;

    private $contentTypeService;

    private $contentService;

    /** @var FieldTypeDataProviderInterface[] */
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

        foreach ($fieldDefinitions as $field) {
            $fieldData = $this->getRandomFieldData($this->contentTypeIdentifier, $field->identifier, $field->fieldTypeIdentifier, $language);
            $contentCreateStruct->setField($field->identifier, $fieldData, $language);
        }

        return $contentCreateStruct;
    }

    public function getFilledContentDataStruct(ContentStruct $contentStruct, $contentItemData, $language): ContentStruct
    {
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier($this->contentTypeIdentifier);
        $fieldDefinitions = $contentType->getFieldDefinitions();

        foreach ($contentItemData as $fieldIdentifier => $value) {
            $fieldDefinition = array_values(array_filter($fieldDefinitions, function (FieldDefinition $fieldDefinition) use ($fieldIdentifier) {
                return $fieldDefinition->identifier === $fieldIdentifier;
            }));

            if (empty($fieldDefinition)) {
                throw new \Exception(sprintf('Could not find fieldIdentifier: %s in content type: %s', $fieldIdentifier, $this->contentTypeIdentifier));
            }

            $fieldData = $this->getFieldDataFromString($fieldDefinition[0]->fieldTypeIdentifier, $value);
            $contentStruct->setField($fieldIdentifier, $fieldData, $language);
        }

        return $contentStruct;
    }

    public function getRandomFieldData(string $contentTypeIdentifier, string $fieldIdentifier, string $fieldTypeIdentifier, $language = 'eng-GB')
    {
        foreach ($this->fieldTypeDataProviders as $provider) {
            if ($provider->supports($fieldTypeIdentifier)) {
                return $provider->generateData($contentTypeIdentifier, $fieldIdentifier, $language);
            }
        }
    }

    public function getFieldDataFromString($fieldIdentifier, $value)
    {
        foreach ($this->fieldTypeDataProviders as $provider) {
            if ($provider->supports($fieldIdentifier)) {
                return $provider->parseFromString($value);
            }
        }
    }
}
