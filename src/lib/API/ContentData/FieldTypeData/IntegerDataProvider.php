<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\ContentData\FieldTypeData;

class IntegerDataProvider extends RandomDataGenerator implements FieldTypeDataProviderInterface
{
    public function supports(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'ezinteger';
    }

    public function generateData(string $language = 'eng-GB')
    {
        return (int) $this->getFaker()->numerify('########');
    }

    public function parseFromString(string $value)
    {
        return (int) $value;
    }
}
