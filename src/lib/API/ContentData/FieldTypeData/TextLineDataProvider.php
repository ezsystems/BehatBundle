<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\ContentData\FieldTypeData;

class TextLineDataProvider extends AbstractFieldTypeDataProvider
{
    public function supports(string $fieldTypeIdentifier): bool
    {
        return 'ezstring' === $fieldTypeIdentifier;
    }

    public function generateData(string $contentTypeIdentifier, string $fieldIdentifier, string $language = 'eng-GB'): string
    {
        $this->setLanguage($language);

        return $this->getFaker()->realText(80, 1);
    }
}
