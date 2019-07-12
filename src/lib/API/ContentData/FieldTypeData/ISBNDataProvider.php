<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\ContentData\FieldTypeData;

class ISBNDataProvider extends RandomDataGenerator implements FieldTypeDataProviderInterface
{
    public function supports(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'ezisbn';
    }

    public function generateData(string $language = 'eng-GB')
    {
        return $this->getFaker()->isbn13;
    }

    public function parseFromString(string $value)
    {
        return $value;
    }
}
