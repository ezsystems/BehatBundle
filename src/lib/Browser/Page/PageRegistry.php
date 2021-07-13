<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Page;

class PageRegistry
{
    /** @var \Ibexa\Behat\Browser\Page\PageInterface[] */
    private $pages;

    public function __construct(iterable $pages)
    {
        foreach ($pages as $page) {
            if (!($page instanceof PageInterface)) {
                throw new \InvalidArgumentException(
                    sprintf('PageRegistry accepts only an array of %s objects!', PageInterface::class)
                );
            }
        }

        $this->pages = $pages;
    }

    public function get(string $pageName): PageInterface
    {
        foreach ($this->pages as $page) {
            if (strtolower($page->getName()) === strtolower($pageName)) {
                return $page;
            }
        }

        throw new \InvalidArgumentException(sprintf('Page with pageName: %s not found.', $pageName));
    }
}
