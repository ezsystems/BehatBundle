<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\ContentData\FieldTypeData;

class StringDataProvider extends RandomDataGenerator implements FieldTypeDataProviderInterface
{
    public function supports(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'ezstring';
    }

    public function generateData(string $language = 'eng-GB'): string
    {
        $this->setLanguage($language);

        return $this->getFaker()->realText(80, 1);
    }

    public function parseFromString(string $value)
    {
        return $value;
    }
}
