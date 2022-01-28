<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\ContentData;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use EzSystems\Behat\API\ContentData\FieldTypeData\FieldTypeDataProviderInterface;

class ContentDataProvider
{
    private $contentTypeIdentifier;

    private $contentTypeService;

    private $contentService;

    /** @var \EzSystems\Behat\API\ContentData\FieldTypeData\FieldTypeDataProviderInterface[] */
    private $fieldTypeDataProviders;

    /** @var \EzSystems\Behat\API\ContentData\RandomDataGenerator */
    private $randomDataGenerator;

    public function __construct(ContentTypeService $contentTypeService, ContentService $contentService, RandomDataGenerator $randomDataGenerator)
    {
        $this->contentTypeService = $contentTypeService;
        $this->contentService = $contentService;
        $this->randomDataGenerator = $randomDataGenerator;
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
        $contentCreateStruct->modificationDate = $this->randomDataGenerator->getRandomDateFromThePast();

        return $this->fillContentStructWithData($contentType, $language, $language, $contentCreateStruct);
    }

    public function getRandomContentUpdateData(string $mainLanguage, string $language): ContentUpdateStruct
    {
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier($this->contentTypeIdentifier);
        $contentUpdateStruct = $this->contentService->newContentUpdateStruct();
        $contentUpdateStruct->initialLanguageCode = $language;

        return $this->fillContentStructWithData($contentType, $mainLanguage, $language, $contentUpdateStruct);
    }

    public function getFilledContentDataStruct(ContentStruct $contentStruct, $contentItemData, $language): ContentStruct
    {
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier($this->contentTypeIdentifier);

        foreach ($contentItemData as $fieldIdentifier => $value) {
            $fieldDefinition = $contentType->getFieldDefinition($fieldIdentifier);

            if (null === $fieldDefinition) {
                throw new \Exception(sprintf('Could not find fieldIdentifier: %s in content type: %s', $fieldIdentifier, $this->contentTypeIdentifier));
            }

            $fieldData = $this->getFieldDataFromString($fieldDefinition->fieldTypeIdentifier, $value);
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

    private function fillContentStructWithData(ContentType $contentType, string $mainLanguage, string $language, ContentStruct $contentStruct): ContentStruct
    {
        $fieldDefinitions = $contentType->getFieldDefinitions()->toArray();

        foreach ($fieldDefinitions as $field) {
            if (!$field->isTranslatable && $mainLanguage !== $language) {
                continue;
            }
            $fieldData = $this->getRandomFieldData($this->contentTypeIdentifier, $field->identifier, $field->fieldTypeIdentifier, $language);
            $contentStruct->setField($field->identifier, $fieldData);
        }

        return $contentStruct;
    }
}
