<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\ContentData\FieldTypeData;

use eZ\Publish\Core\FieldType\Url\Value;

class URLDataProvider extends AbstractFieldTypeDataProvider
{
    public function supports(string $fieldTypeIdentifier): bool
    {
        return 'ezurl' === $fieldTypeIdentifier;
    }

    public function generateData(string $contentTypeIdentifier, string $fieldIdentifier, string $language = 'eng-GB')
    {
        return new Value($this->getFaker()->url, $this->getFaker()->realText(80, 1));
    }

    public function parseFromString(string $value)
    {
        $values = explode(',', $value);
        $url = $values[0];
        $text = $values[1] ?? null;

        return new Value($url, $text);
    }
}
