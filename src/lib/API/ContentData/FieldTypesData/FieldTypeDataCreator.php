<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\ContentData\FieldTypesData;

use eZ\Publish\Core\MVC\Symfony\Locale\LocaleConverterInterface;
use Faker;

class FieldTypeDataCreator
{
    private const RICHTEXT_XML = '<?xml version="1.0" encoding="UTF-8"?>
<section xmlns="http://docbook.org/ns/docbook" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:ezxhtml="http://ez.no/xmlns/ezpublish/docbook/xhtml" xmlns:ezcustom="http://ez.no/xmlns/ezpublish/docbook/custom" version="5.0-variant ezpublish-1.0">
<title ezxhtml:level="2">%s</title>
</section>';

    private $localeConverter;

    public function __construct(LocaleConverterInterface $localeConverter)
    {
        $this->localeConverter = $localeConverter;
    }

    public function getData(string $fieldtype, $language)
    {
        $faker = Faker\Factory::create($this->localeConverter->convertToPOSIX($language));

        switch ($fieldtype)
        {
            case 'ezrichtext': return sprintf(self::RICHTEXT_XML, $faker->text);
            default: return $faker->text(50);
        }
    }
}