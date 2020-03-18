<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use EzSystems\Behat\API\Facade\ContentFacade;
use EzSystems\Behat\Core\Behat\ArgumentParser;

class ContentContext implements Context
{
    /** @var ContentFacade */
    private $contentFacade;

    /** @var ArgumentParser */
    private $argumentParser;

    public function __construct(ContentFacade $contentFacade, ArgumentParser $argumentParser)
    {
        $this->contentFacade = $contentFacade;
        $this->argumentParser = $argumentParser;
    }

    /**
     * @Given I create :number :contentTypeIdentifier Content items in :parentUrl in :language
     */
    public function createMultipleContentItems(string $numberOfItems, string $contentTypeIdentifier, string $parentUrl, $language): void
    {
        $parentUrl = $this->argumentParser->parseUrl($parentUrl);

        for ($i = 0; $i < $numberOfItems; ++$i) {
            $this->contentFacade->createContent($contentTypeIdentifier, $parentUrl, $language);
        }
    }

    /**
     * @Given I create :contentTypeIdentifier Content items in :parentUrl in :language
     */
    public function createContentItems($contentTypeIdentifier, $parentUrl, $language, TableNode $contentItemsData): void
    {
        $parentUrl = $this->argumentParser->parseUrl($parentUrl);
        $parsedContentItemData = $this->parseData($contentItemsData);

        foreach ($parsedContentItemData as $contentItemData) {
            $this->contentFacade->createContent($contentTypeIdentifier, $parentUrl, $language, $contentItemData);
        }
    }

    /**
     * @Given I edit :locationURL Content item in :language
     */
    public function editContentItem($locationURL, $language, TableNode $contentItemsData): void
    {
        $locationURL = $this->argumentParser->parseUrl($locationURL);
        $parsedContentItemData = $this->parseData($contentItemsData);

        foreach ($parsedContentItemData as $contentItemData) {
            $this->contentFacade->editContent($locationURL, $language, $contentItemData);
        }
    }

    private function parseData(TableNode $contentItemData)
    {
        return $contentItemData->getHash();
    }
}
