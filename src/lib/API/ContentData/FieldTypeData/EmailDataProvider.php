<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\ContentData\FieldTypeData;

class EmailDataProvider extends RandomDataGenerator implements FieldTypeDataProviderInterface
{
    public function supports(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'ezemail' || $fieldTypeIdentifier === 'email';
    }

    public function generateData(string $language = 'eng-GB'): string
    {
        $this->setLanguage($language);

        return $this->getFaker()->companyEmail;
    }

    public function parseFromString(string $value)
    {
        return $value;
    }
}
