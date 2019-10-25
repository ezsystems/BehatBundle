<?php

namespace EzSystems\Behat\Test\MinkElementDecorator;

interface ExtendedElementInterface
{
    public function findVisible(string $selector, string $locator): ExtendedElementInterface;

    public function findAllVisible(string $selector, string $locator): NodeElementCollection;

    public function waitForElementToDisappear($selector, $locator): void;
}