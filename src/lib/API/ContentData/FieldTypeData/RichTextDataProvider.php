<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\ContentData\FieldTypeData;

class RichTextDataProvider extends RandomDataGenerator implements FieldTypeDataProviderInterface
{
    private const RICHTEXT_XML = '<?xml version="1.0" encoding="UTF-8"?>
<section xmlns="http://docbook.org/ns/docbook" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:ezxhtml="http://ez.no/xmlns/ezpublish/docbook/xhtml" xmlns:ezcustom="http://ez.no/xmlns/ezpublish/docbook/custom" version="5.0-variant ezpublish-1.0">
<title ezxhtml:level="2">%s</title>
</section>';

    public function canWork(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'ezrichtext';
    }

    public function generateData(string $language)
    {
        $this->setLanguage($language);

        return sprintf(self::RICHTEXT_XML, $this->faker->realText());
    }
}
