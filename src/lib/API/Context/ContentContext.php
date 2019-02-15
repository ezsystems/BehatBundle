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
     * @Given I create :number :contentTypeIdentifier Content items in :parentPathString
     */
    public function createMultipleContentItems(string $numberOfItems, string $contentTypeIdentifier, string $parentPathString): void
    {
        $this->contentFacade->setUser("admin");

        for ($i = 0; $i < $numberOfItems; $i++) {
            $contentItemData = $this->contentFacade->getRandomContentData($contentTypeIdentifier);
            $this->contentFacade->createContent($contentTypeIdentifier, $parentPathString, $contentItemData);
        }
    }

    /**
     * @Given I create :contentTypeIdentifier Content items in :parentUrl
     */
    public function createContentItems($contentTypeIdentifier, $parentUrl, TableNode $contentItemsData): void
    {
        $this->contentFacade->setUser("admin");

        foreach ($contentItemsData as $contentItemData) {
            $parsedContentItemData = $this->parseData($contentItemData);
            $this->contentFacade->createContent($contentTypeIdentifier, $parentUrl, $parsedContentItemData);
        }
    }

    private function parseData($contentItemData)
    {
        return null;
    }
}