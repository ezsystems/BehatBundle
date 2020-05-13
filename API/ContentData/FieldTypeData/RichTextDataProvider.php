<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\ContentData\FieldTypeData;

use eZ\Publish\API\Repository\URLService;
use EzSystems\BehatBundle\API\ContentData\RandomDataGenerator;
use EzSystems\BehatBundle\API\Facade\SearchFacade;

class RichTextDataProvider extends AbstractFieldTypeDataProvider
{
    private const SIMPLE_RICHTEXT_XML = '<?xml version="1.0" encoding="UTF-8"?>
<section xmlns="http://docbook.org/ns/docbook" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:ezxhtml="http://ez.no/xmlns/ezpublish/docbook/xhtml" xmlns:ezcustom="http://ez.no/xmlns/ezpublish/docbook/custom" version="5.0-variant ezpublish-1.0">
<title ezxhtml:level="2">%s</title>
</section>';

    private const COMPLEX_RICHTEXT_XML = '<section xmlns="http://docbook.org/ns/docbook" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:ezxhtml="http://ez.no/xmlns/ezpublish/docbook/xhtml" xmlns:ezcustom="http://ez.no/xmlns/ezpublish/docbook/custom" version="5.0-variant ezpublish-1.0">
<title ezxhtml:level="2">%s</title>
<para>Example embed: <ezembedinline xlink:href="ezcontent://%d" view="embed-inline"/></para>
<para xml:id="anchor" ezxhtml:class="ez-has-anchor">Example link: <link xlink:href="ezlocation://%d" xlink:show="none">%s</link></para>
</section>';

    /**
     * @var SearchFacade
     */
    private $searchFacade;

    public function __construct(RandomDataGenerator $randomDataGenerator, SearchFacade $searchFacade)
    {
        parent::__construct($randomDataGenerator);
        $this->searchFacade = $searchFacade;
    }

    public function supports(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'ezrichtext';
    }

    public function generateData(string $contentTypeIdentifier, string $fieldIdentifier, string $language = 'eng-GB'): string
    {
        $this->setLanguage($language);

        return sprintf(
            self::COMPLEX_RICHTEXT_XML,
            $this->getFaker()->realText(),
            $this->searchFacade->getRandomContentIds(1),
            $this->searchFacade->getRandomLocationId(),
            $this->getFaker()->realText()
        );
    }

    public function parseFromString(string $value)
    {
        return sprintf(self::SIMPLE_RICHTEXT_XML, $value);
    }
}
