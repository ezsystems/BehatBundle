<?php

namespace EzSystems\Behat\Test\MinkElementDecorator;

use Behat\Mink\Element\TraversableElement;
use Behat\Mink\Session;

class ExtendedElementActions implements ExtendedElementInterface
{
    protected $decoratedElement;

    // TODO: Add timeout setting. If fluent interface - also set the timeout for new element, to be consistent?
    private $timeout;

    public function __construct(TraversableElement $decoratedElement)
    {
        $this->decoratedElement = $decoratedElement;
    }

    public function waitFor($timeoutSeconds, $callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Given callback is not a valid callable');
        }

        $start = time();
        $end = $start + $timeoutSeconds;
        do {
            try {
                $result = $callback($this);
                if ($result) {
                    return $result;
                }
            } catch (\Exception $e) {
            }
            usleep(250 * 1000);
        } while (time() < $end);

        return null;
    }

    public function findAll($selector, $locator): NodeElementCollection
    {
        $elements = $this->waitFor($this->timeout, function () use ($selector, $locator) {
            $elements = $this->decoratedElement->findAll($selector, $locator);
            foreach ($elements as $element) {
                if (!$element->isValid()) {
                    return false;
                }
            }

            return $elements;
        });

        $wrappedElements = [];

        foreach ($elements as $element) {
            $wrappedElements[] = new ExtendedNodeElement($element);
        }

        return new NodeElementCollection($wrappedElements);
    }

    public function find($selector, $locator): ExtendedElementInterface
    {
        return $this->waitFor($this->timeout,
            function () use ($selector, $locator) {
                return new ExtendedNodeElement(new ExtendedElementActions($this->decoratedElement->find($selector, $locator)));
            });
    }

    public function findAllVisible($selector, $locator): NodeElementCollection
    {
        return $this->findAll($selector, $locator)->getVisibleElements();
    }

    public function findVisible($selector, $locator): ExtendedElementInterface
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

    public function getSession(): Session
    {
        return $this->decoratedElement->getSession();
    }

    public function getXPath(): string
    {
        return $this->decoratedElement->getXpath();
    }

}