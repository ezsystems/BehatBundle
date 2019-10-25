<?php

namespace EzSystems\Behat\Test\Factory;

use EzSystems\Behat\Test\PageObject\PageInterface;

final class PageObjectFactory implements PageObjectFactoryInterface
{
    /** @var PageInterface[] */
    private $pages;

    public function __construct()
    {
        $this->pages = [];
    }

    public function create(string $pageName): PageInterface
    {
        foreach ($this->pages as $page) {
            if ($page->getName() === $pageName) {
                return $page;
            }
        }
    }

    public function getPreviewType(string $pageType): string
    {
        // TODO: Implement getPreviewType() method.
    }

    public function add(PageInterface $page): void
    {
        $this->pages[] = $page;
    }
}