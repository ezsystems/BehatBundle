<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use EzSystems\BehatBundle\API\Facade\ContentFacade;

class ContentContext implements Context
{
    /** @var ContentFacade  */
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
        $this->contentFacade->setUser("admin");

        if ($parentUrl === 'root')
        {
            $parentUrl = '/';
        }

        for ($i = 0; $i < $numberOfItems; $i++) {
            $this->contentFacade->createContent($contentTypeIdentifier, $parentUrl, $language);
        }
    }

    /**
     * @Given I create :contentTypeIdentifier Content items in :parentUrl in :language
     */
    public function createContentItems($contentTypeIdentifier, $parentUrl, $language, TableNode $contentItemsData): void
    {
        $this->contentFacade->setUser("admin");

        if ($parentUrl === 'root')
        {
            $parentUrl = '/';
        }

        $parsedContentItemData = $this->parseData($contentItemsData);

        foreach ($parsedContentItemData as $contentItemData) {
            $this->contentFacade->createContent($contentTypeIdentifier, $parentUrl, $language, $contentItemData);
        }
    }

    private function parseData(TableNode $contentItemData)
    {
        // TODO: think of a way to handle arrays in the future, should happen in this layer
        return $contentItemData->getHash();
    }
}