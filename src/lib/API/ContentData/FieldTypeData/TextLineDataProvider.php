<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\ContentData\FieldTypeData;

class TextLineDataProvider extends RandomDataGenerator implements FieldTypeDataProviderInterface
{
    public function canWork(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'ezstring';
    }

    public function generateData(string $language)
    {
        $this->setLanguage($language);
        return $this->faker->text(20);
    }
}
