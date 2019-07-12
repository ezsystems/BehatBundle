<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\ContentData\FieldTypeData;

class TextLineDataProvider extends RandomDataGenerator implements FieldTypeDataProviderInterface
{
    public function supports(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'eztext';
    }

    public function generateData(string $language = 'eng-GB'): string
    {
        $this->setLanguage($language);

        return $this->getFaker()->paragraphs(5, true);
    }

    public function parseFromString(string $value)
    {
        return $value;
    }
}
