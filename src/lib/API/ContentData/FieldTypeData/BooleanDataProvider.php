<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\ContentData\FieldTypeData;

class BooleanDataProvider implements FieldTypeDataProviderInterface
{
    public function supports(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'ezboolean';
    }

    public function generateData(string $language = 'eng-GB')
    {
        // if the field is required then the value has to be true.
        return true;
    }

    public function parseFromString(string $value)
    {
        return  $value === 'true';
    }
}
