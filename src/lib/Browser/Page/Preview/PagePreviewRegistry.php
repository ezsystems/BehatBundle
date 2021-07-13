<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Page\Preview;

class PagePreviewRegistry
{
    /** @var \Ibexa\Behat\Browser\Page\Preview\PagePreviewInterface[] */
    private $pagePreviews;

    public function __construct(iterable $pagePreviews)
    {
        foreach ($pagePreviews as $pagePreview) {
            if (!($pagePreview instanceof PagePreviewInterface)) {
                throw new \InvalidArgumentException(
                    sprintf('PagePreviewRegistry accepts only an array of %s objects!', PagePreviewInterface::class)
                );
            }
        }

        $this->pagePreviews = $pagePreviews;
    }

    public function getPreview(string $contentTypeIdentifier, string $viewType = 'full'): PagePreviewInterface
    {
        foreach ($this->pagePreviews as $pagePreview) {
            if ($pagePreview->supports($contentTypeIdentifier, $viewType)) {
                return $pagePreview;
            }
        }

        throw new \InvalidArgumentException(sprintf('Page Preview supporting %s %s not found.', $contentTypeIdentifier, $viewType));
    }
}
