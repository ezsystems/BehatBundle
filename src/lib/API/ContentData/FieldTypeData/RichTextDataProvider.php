<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\ContentData\FieldTypeData;

use EzSystems\Behat\API\ContentData\RandomDataGenerator;
use EzSystems\Behat\API\Facade\SearchFacade;

class RichTextDataProvider extends AbstractFieldTypeDataProvider
{
    private const SIMPLE_RICHTEXT_XML = '<?xml version="1.0" encoding="UTF-8"?>
<section xmlns="http://docbook.org/ns/docbook" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:ezxhtml="http://ibexa.co/xmlns/dxp/docbook/xhtml" xmlns:ezcustom="http://ibexa.co/xmlns/dxp/docbook/custom" version="5.0-variant ezpublish-1.0">
<title ezxhtml:level="2">%s</title>
</section>';

    private const COMPLEX_RICHTEXT_XML = '<?xml version="1.0" encoding="UTF-8"?>
<section xmlns="http://docbook.org/ns/docbook" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:ezxhtml="http://ibexa.co/xmlns/dxp/docbook/xhtml" xmlns:ezcustom="http://ibexa.co/xmlns/dxp/docbook/custom" version="5.0-variant ezpublish-1.0">
<title ezxhtml:level="2">%s</title>
<para><link xlink:href="%s" xlink:show="none" xlink:title="%s">%s</link></para>
</section>';

    /**
     * @var \EzSystems\Behat\API\Facade\SearchFacade
     */
    private $searchFacade;

    public function __construct(RandomDataGenerator $randomDataGenerator, SearchFacade $searchFacade)
    {
        parent::__construct($randomDataGenerator);
        $this->searchFacade = $searchFacade;
    }

    public function supports(string $fieldTypeIdentifier): bool
    {
        return 'ezrichtext' === $fieldTypeIdentifier;
    }

    public function generateData(string $contentTypeIdentifier, string $fieldIdentifier, string $language = 'eng-GB'): string
    {
        $this->setLanguage($language);

        $randomNumber = $this->randomDataGenerator->getRandomProbability();

        return $randomNumber <= 0.9 ?
            sprintf(self::SIMPLE_RICHTEXT_XML, $this->getFaker()->realText()) :
            sprintf(
                self::COMPLEX_RICHTEXT_XML,
                $this->getFaker()->realText(),
                $this->getFaker()->url,
                $this->getFaker()->word,
                $this->getFaker()->realText()
            );
    }

    public function parseFromString(string $value)
    {
        return sprintf(self::SIMPLE_RICHTEXT_XML, $value);
    }
}
