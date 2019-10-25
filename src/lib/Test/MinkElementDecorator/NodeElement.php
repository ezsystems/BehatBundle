<?php

namespace EzSystems\Behat\Test\MinkElementDecorator;

use Behat\Mink\Element\NodeElement as MinkNodeElement;

class NodeElement extends MinkNodeElement
{
    public $timeout;

    public function __construct(MinkNodeElement $node)
    {
        parent::__construct($node->getXpath(), $node->getSession());
        $this->timeout = 10;
    }

    public function findAll($selector, $locator): NodeElementCollection
    {
        $elements = $this->waitFor($this->timeout, function () use ($selector, $locator) {
            $elements = parent::findAll($selector, $locator);
            foreach ($elements as $element) {
                if (!$element->isValid()) {
                    return false;
                }
            }

            return $elements;
        });

        $wrappedElements = [];

        foreach ($elements as $element) {
            $wrappedElements[] = new NodeElement($element, $this->getSession());
        }

        return new NodeElementCollection($wrappedElements);
    }

    public function find($selector, $locator): \Behat\Mink\Element\NodeElement
    {
        return $this->waitFor($this->timeout,
                function () use ($selector, $locator) {
                    return parent::find($selector, $locator);
                }) ?? new NullElement();
    }

    public function findAllVisible($selector, $locator): NodeElementCollection
    {
        return $this->findAll($selector, $locator)->getVisibleElements();
    }

    public function findVisible($selector, $locator): NodeElementCollection
    {
        return current($this->findAll($selector, $locator)->getVisibleElements());
    }

    public function waitForElementToDisappear($selector, $locator): void
    {
        $currentTimeoutValue = $this->timeout;

        $this->waitFor($this->timeout, function () use ($selector, $locator) {
            $this->timeout = 1;
            return $this->find($selector, $locator)->isVisible() === false;
        });

        $this->timeout = $currentTimeoutValue;
    }
}