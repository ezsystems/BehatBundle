<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\ContentData\FieldTypeData;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Core\FieldType\Selection\Value;

class SelectionDataProvider implements FieldTypeDataProviderInterface
{
    private $contentTypeService;

    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    public function supports(string $fieldTypeIdentifier): bool
    {
        return 'ezselection' === $fieldTypeIdentifier;
    }

    public function generateData(string $contentTypeIdentifier, string $fieldIdentifier, string $language = 'eng-GB')
    {
        $fieldSettings = $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier)->getFieldDefinition($fieldIdentifier)->getFieldSettings();

        $isMultiple = $fieldSettings['isMultiple'];
        $availableOptions = $fieldSettings['options'];
        if (empty($availableOptions)) {
            return new Value();
        }

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
