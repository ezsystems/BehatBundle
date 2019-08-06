<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\ContentData\FieldTypeData;

interface FieldTypeDataProviderInterface
{
    public const SERVICE_TAG = 'ezsystems.behat.fieldtype_data_provider';

    public function supports(string $fieldTypeIdentifier): bool;

    public function generateData(string $language = 'eng-GB');

    public function parseFromString(string $value);
}
