<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Page\Preview;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Locator\CSSLocator;

class FolderPreview extends Component implements PagePreviewInterface
{
    /** @var string */
    private $expectedPageTitle;

    public function setExpectedPreviewData(array $data)
    {
        $this->expectedPageTitle = $data['title'];
    }

    public function verifyPreviewData()
    {
        $this->getHTMLPage()->find($this->getLocator('title'))->assert()->textEquals($this->expectedPageTitle);
    }

    public function supports(string $contentTypeIdentifier, string $viewType): bool
    {
        $contentTypeIdentifier = strtolower($contentTypeIdentifier);

        return 'folder' === $contentTypeIdentifier || 'dedicatedfolder' === $contentTypeIdentifier;
    }

    public function verifyIsLoaded(): void
    {
        $this->verifyPreviewData();
    }

    protected function specifyLocators(): array
    {
        return [
            new CSSLocator('title', 'h2'),
        ];
    }
}
