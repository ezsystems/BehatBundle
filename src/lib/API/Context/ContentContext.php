<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use EzSystems\Behat\API\Facade\ContentFacade;
use EzSystems\Behat\Core\Behat\ArgumentParser;

class ContentContext implements Context
{
    /** @var \EzSystems\Behat\API\Facade\ContentFacade */
    private $contentFacade;

    /** @var \EzSystems\Behat\Core\Behat\ArgumentParser */
    private $argumentParser;

    public function __construct(ContentFacade $contentFacade, ArgumentParser $argumentParser)
    {
        $this->contentFacade = $contentFacade;
        $this->argumentParser = $argumentParser;
    }

    /**
     * @Given I create :number :contentTypeIdentifier Content items in :parentUrl in :language
     *
     * @param mixed $language
     */
    public function createMultipleContentItems(string $numberOfItems, string $contentTypeIdentifier, string $parentUrl, $language): void
    {
        $parentUrl = $this->argumentParser->parseUrl($parentUrl);

        for ($i = 0; $i < $numberOfItems; ++$i) {
            $this->contentFacade->createContent($contentTypeIdentifier, $parentUrl, $language);
        }
    }

    /**
     * @Given a :contentTypeIdentifier Content item named :contentName exists in :parentUrl
     */
    public function contentItemExists(string $contentTypeIdentifier, string $contentName, string $parentUrl, TableNode $contentItemData): void
    {
        $parentUrl = $this->argumentParser->parseUrl($parentUrl);
        $contentUrl = sprintf('%s/%s', $parentUrl, $this->argumentParser->parseUrl($contentName));
        $contentData = $this->parseData($contentItemData)[0];
        $this->contentFacade->createContentIfNotExists($contentTypeIdentifier, $contentUrl, $parentUrl, $contentData);
    }

    /**
     * @Given I create :contentTypeIdentifier Content items in :parentUrl in :language
     *
     * @param mixed $contentTypeIdentifier
     * @param mixed $parentUrl
     * @param mixed $language
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
     * @Given I create :contentTypeIdentifier Content items
     *
     * @param mixed $contentTypeIdentifier
     */
    public function createContentItemsInDifferentLocations($contentTypeIdentifier, TableNode $contentItemsData): void
    {
        $parsedContentItemData = $this->parseData($contentItemsData);

        foreach ($parsedContentItemData as $contentItemData) {
            $parentUrl = $this->argumentParser->parseUrl($contentItemData['parentPath']);
            $language = $contentItemData['language'];
            unset($contentItemData['parentPath'], $contentItemData['language']);

            $this->contentFacade->createContent($contentTypeIdentifier, $parentUrl, $language, $contentItemData);
        }
    }

    /**
     * @Given I create :contentTypeIdentifier Content drafts
     *
     * @param mixed $contentTypeIdentifier
     */
    public function createContentDraftsInDifferentLocations($contentTypeIdentifier, TableNode $contentItemsData): void
    {
        $parsedContentItemData = $this->parseData($contentItemsData);

        foreach ($parsedContentItemData as $contentItemData) {
            $parentUrl = $this->argumentParser->parseUrl($contentItemData['parentPath']);
            $language = $contentItemData['language'];
            unset($contentItemData['parentPath'], $contentItemData['language']);

            $this->contentFacade->createContentDraft($contentTypeIdentifier, $parentUrl, $language, $contentItemData);
        }
    }

    /**
     * @Given I edit :locationURL Content item in :language
     *
     * @param mixed $locationURL
     * @param mixed $language
     */
    public function editContentItem($locationURL, $language, TableNode $contentItemsData): void
    {
        $locationURL = $this->argumentParser->parseUrl($locationURL);
        $parsedContentItemData = $this->parseData($contentItemsData);

        foreach ($parsedContentItemData as $contentItemData) {
            $this->contentFacade->editContent($locationURL, $language, $contentItemData);
        }
    }

    /**
     * @Given I create a new Draft for :locationURL Content item in :language
     *
     * @param mixed $locationURL
     * @param mixed $language
     */
    public function createNewDraftForExistingItem($locationURL, $language, TableNode $contentItemsData): void
    {
        $locationURL = $this->argumentParser->parseUrl($locationURL);
        $parsedContentItemData = $this->parseData($contentItemsData);

        foreach ($parsedContentItemData as $contentItemData) {
            $this->contentFacade->createDraftForExistingContent($locationURL, $language, $contentItemData);
        }
    }

    private function parseData(TableNode $contentItemData)
    {
        return $contentItemData->getHash();
    }
}
