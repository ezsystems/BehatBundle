<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\ContentData\FieldTypeData;


class EmailDataProvider extends RandomDataGenerator implements FieldTypeDataProviderInterface
{
    public function canWork(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'ezemail' || $fieldTypeIdentifier === 'email';
    }

    public function generateData(string $language): string
    {
        $this->setLanguage($language);
        return $this->faker->companyEmail;
    }
}