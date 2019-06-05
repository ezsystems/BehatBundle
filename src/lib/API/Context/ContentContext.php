<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use EzSystems\Behat\API\Facade\ContentFacade;

class ContentContext implements Context
{
    /** @var ContentFacade */
    private $contentFacade;

    public function __construct(ContentFacade $contentFacade)
    {
        $this->contentFacade = $contentFacade;
    }

    /**
     * @Given I create :number :contentTypeIdentifier Content items in :parentUrl in :language
     */
    public function createMultipleContentItems(string $numberOfItems, string $contentTypeIdentifier, string $parentUrl, $language): void
    {
        $parentUrl = $this->parseUrl($parentUrl);

        for ($i = 0; $i < $numberOfItems; ++$i) {
            $this->contentFacade->createContent($contentTypeIdentifier, $parentUrl, $language);
            gc_collect_cycles();
        }
        gc_collect_cycles();
    }

    /**
     * @Given I create :contentTypeIdentifier Content items in :parentUrl in :language
     */
    public function createContentItems($contentTypeIdentifier, $parentUrl, $language, TableNode $contentItemsData): void
    {
        $parentUrl = $this->parseUrl($parentUrl);
        $parsedContentItemData = $this->parseData($contentItemsData);

        foreach ($parsedContentItemData as $contentItemData) {
            $this->contentFacade->createContent($contentTypeIdentifier, $parentUrl, $language, $contentItemData);
        }
    }

    private function parseUrl(string $url)
    {
        if ($url === 'root') {
            return '/';
        }

        $url = str_replace(' ', '-', $url);

        return strpos($url, '/') === 0 ? $url : sprintf('/%s', $url);
    }

    private function parseData(TableNode $contentItemData)
    {
        return $contentItemData->getHash();
    }
}
