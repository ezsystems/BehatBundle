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

    private const COMPLEX_RICHTEXT_XML = '<?xml version="1.0" encoding="UTF-8"?>
<section xmlns="http://docbook.org/ns/docbook" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:ezxhtml="http://ez.no/xmlns/ezpublish/docbook/xhtml" xmlns:ezcustom="http://ez.no/xmlns/ezpublish/docbook/custom" version="5.0-variant ezpublish-1.0">
<title ezxhtml:level="2">%s</title>
<para><link xlink:href="%s" xlink:show="none" xlink:title="%s">%s</link></para>
</section>';

    /**
     * @var \EzSystems\BehatBundle\API\Facade\SearchFacade
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

        $randomNumber = $this->randomDataGenerator->getRandomProbability();

        $text = $randomNumber <= 0.9 ?
            sprintf(self::SIMPLE_RICHTEXT_XML, $this->getFaker()->realText()) :
            sprintf(
                self::COMPLEX_RICHTEXT_XML,
                $this->getFaker()->realText(),
                $this->getFaker()->url,
                $this->getFaker()->word,
                $this->getFaker()->realText()
            );

        return $text;
    }

    public function parseFromString(string $value)
    {
        return sprintf(self::SIMPLE_RICHTEXT_XML, $value);
    }
}
