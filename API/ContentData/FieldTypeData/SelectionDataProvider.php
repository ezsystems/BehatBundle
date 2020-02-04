<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\ContentData\FieldTypeData;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\FieldType\Selection\Value;

class SelectionDataProvider implements FieldTypeDataProviderInterface
{
    private $contentTypeService;

    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    public function supports(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'ezselection';
    }

    public function generateData(string $contentTypeIdentifier, string $fieldIdentifier, string $language = 'eng-GB')
    {
        $fieldSettings = $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier)->getFieldDefinition($fieldIdentifier)->getFieldSettings();

        $isMultiple = $fieldSettings['isMultiple'];
        $availableOptions = $fieldSettings['options'];

        $numberOfOptionsToPick = $isMultiple ? random_int(1, count($availableOptions)) : 1;
        $randomOptionIndices = array_rand(range(0, count($availableOptions) - 1), $numberOfOptionsToPick);

        $randomOptionIndices = is_array($randomOptionIndices) ? $randomOptionIndices : [$randomOptionIndices];

        return new Value($randomOptionIndices);
    }

    public function parseFromString(string $value)
    {
        $options = explode(',', $value);

        return new Value($options);
    }
}
