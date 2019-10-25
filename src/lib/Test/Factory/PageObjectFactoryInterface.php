<?php


namespace EzSystems\Behat\Test\Factory;

use EzSystems\Behat\Test\PageObject\PageInterface;

interface PageObjectFactoryInterface
{
    public const PAGE_OBJECT_FACTORY_TAG = 'ezplatform.behat.page_object_factory';

    public function create(string $pageType): PageInterface;

    public function getPreviewType(string $pageType): string;

    public function add(PageInterface $page): void;
}