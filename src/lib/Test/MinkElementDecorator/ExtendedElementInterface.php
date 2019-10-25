<?php

namespace EzSystems\Behat\Test\MinkElementDecorator;

use Behat\Mink\Element\ElementInterface;

interface ExtendedElementInterface extends ElementInterface
{
    // dont extend, ElementInterface does not specify return types

    public function findVisible(string $selector, string $locator): NodeElement;

    public function findAllVisible(string $selector, string $locator): NodeElementCollection;

    public function waitForElementToDisappear($selector, $locator): void;

    public function uploadFile(string $localFileName): string;

    public function moveWithHover(string $startExpression, string $hoverExpression, string $placeholderExpression): void;
}