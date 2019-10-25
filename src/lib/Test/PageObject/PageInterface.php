<?php

namespace EzSystems\Behat\Test\PageObject;

interface PageInterface
{
    public const PAGE_OBJECT_TAG = 'ezplatform.behat.page_object';

    public function open(): void;

    public function tryToOpen(): void;

    public function verifyIsLoaded(): void;

    public function getName(): string;
}