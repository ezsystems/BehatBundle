<?php

namespace EzSystems\Behat\Test\MinkElementDecorator;

use Behat\Mink\Element\TraversableElement;
use Behat\Mink\Session;

class ExtendedElementActions implements ExtendedElementInterface
{
    protected $decoratedElement;

    private $timeout;

    public function __construct(TraversableElement $decoratedElement)
    {
        $this->decoratedElement = $decoratedElement;
    }

    public function setTimeout(int $timeoutSeconds): void
    {
        $this->timeout = $timeoutSeconds;
    }

    public function waitFor(int $timeoutSeconds, callable $callback)
    {
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
        if ($this->decoratedElement === null) {
            return new NodeElementCollection([]);
        }

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
            $wrappedElement = new ExtendedNodeElement(new ExtendedElementActions($element));
            $wrappedElement->setTimeout($this->timeout);
            $wrappedElements[] = $wrappedElement;
        }

        return new NodeElementCollection($wrappedElements);
    }

    public function find($selector, $locator): ExtendedElementInterface
    {
        if ($this->decoratedElement === null) {
            return null;
        }

        return $this->waitFor($this->timeout,
            function () use ($selector, $locator) {
                $extendedElementActions = new ExtendedElementActions($this->decoratedElement->find($selector, $locator));
                $extendedElementActions->setTimeout($this->timeout);
                return new ExtendedNodeElement($extendedElementActions);
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