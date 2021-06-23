<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\ContentData\FieldTypeData;

interface FieldTypeDataProviderInterface
{
    public const SERVICE_TAG = 'ezplatform.behat.fieldtype_data_provider';

    public function supports(string $fieldTypeIdentifier): bool;

    public function generateData(string $contentTypeIdentifier, string $fieldIdentifier, string $language = 'eng-GB');

    public function parseFromString(string $value);
}
