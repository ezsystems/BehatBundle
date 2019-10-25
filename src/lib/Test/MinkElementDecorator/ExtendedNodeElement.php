<?php

namespace EzSystems\Behat\Test\MinkElementDecorator;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;

class ExtendedNodeElement extends NodeElement implements ExtendedElementInterface
{
    /**
     * @var ExtendedElementActions
     */
    private $extendedActions;

    public function __construct(ExtendedElementActions $extendedActions)
    {
        parent::__construct($extendedActions->getXPath(), $extendedActions->getSession());
        $this->extendedActions = $extendedActions;
    }

    public function find($selector, $locator): ExtendedElementInterface
    {
        return $this->extendedActions->find($selector, $locator);
    }

    public function findAll($selector, $locator): NodeElementCollection
    {
        return $this->extendedActions->findAll($selector, $locator);
    }

    public function findVisible(string $selector, string $locator): ExtendedElementInterface
    {
        return $this->extendedActions->findVisible($selector, $locator);
    }

    public function findAllVisible(string $selector, string $locator): NodeElementCollection
    {
        return $this->extendedActions->findAllVisible($selector, $locator);
    }

    public function waitForElementToDisappear($selector, $locator): void
    {
        $this->extendedActions->waitForElementToDisappear($selector, $locator);
    }
}