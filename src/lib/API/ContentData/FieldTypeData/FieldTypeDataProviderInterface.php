<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\ContentData\FieldTypeData;

interface FieldTypeDataProviderInterface
{
    public const SERVICE_TAG = 'behat_fieldtype_data_provider';

    public function canWork(string $fieldTypeIdentifier): bool;

    public function generateData(string $language);
}