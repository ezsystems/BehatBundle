<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Context;

use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Ibexa\Behat\Browser\Page\Preview\PagePreviewRegistry;

class ContentPreviewContext extends RawMinkContext
{
    /** @var \Ibexa\Behat\Browser\Page\Preview\PagePreviewRegistry */
    private $pagePreviewRegistry;

    public function __construct(PagePreviewRegistry $pagePreviewRegistry)
    {
        $this->pagePreviewRegistry = $pagePreviewRegistry;
    }

    /**
     * @Given I see correct preview data for :contentTypeName Content Type
     */
    public function iSeeCorrectPreviewDataFor(string $contentType, TableNode $previewData): void
    {
        var_dump($this->getSession()->getPage()->getOuterHtml());
        $preview = $this->pagePreviewRegistry->getPreview($contentType);
        $preview->setExpectedPreviewData(['title' => $previewData->getHash()[0]['value']]);
        $preview->verifyPreviewData();
    }
}
