<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element;

use Ibexa\Behat\Browser\Element\Condition\ConditionInterface;
use Ibexa\Behat\Browser\Exception\TimeoutException;
use Ibexa\Behat\Browser\Locator\LocatorInterface;
use Traversable;

class BaseElement implements BaseElementInterface
{
    /** @var int */
    protected $timeout = 1;

    /** @var \Behat\Mink\Element\TraversableElement */
    protected $decoratedElement;

    public function setTimeout(int $timeoutSeconds): BaseElementInterface
    {
        $this->timeout = $timeoutSeconds;

        return $this;
    }

    public function waitUntilCondition(ConditionInterface $condition): BaseElementInterface
    {
        $start = time();
        $end = $start + $this->timeout;
        do {
            try {
                if ($condition->isMet()) {
                    return $this;
                }
            } catch (\Exception $e) {
            }
            usleep(100 * 1000);
        } while (time() < $end);

        throw new TimeoutException($condition->getErrorMessage());
    }

    public function waitUntil(callable $callback, string $errorMessage)
    {
        $start = time();
        $end = $start + $this->timeout;
        $caughtException = null;
        do {
            try {
                $result = $callback($this);
                if ($result) {
                    return $result;
                }
            } catch (\Exception $e) {
                $caughtException = $e;
            }
            usleep(100 * 1000);
        } while (time() < $end);

        throw new TimeoutException($errorMessage, 0, $caughtException);
    }

    public function find(LocatorInterface $locator): ElementInterface
    {
        return $this->waitUntil(
            function () use ($locator) {
                $minkFoundElement = $this->decoratedElement->find($locator->getType(), $locator->getSelector());

                $foundElement = new Element($locator, $minkFoundElement);
                if (!$locator->elementMeetsCriteria($foundElement)) {
                    return false;
                }

                return $foundElement;
            },
            sprintf(
                "%s selector '%s': '%s' not found in %d seconds.",
                $locator->getType(),
                $locator->getIdentifier(),
                $locator->getSelector(),
                $this->timeout
            )
        );
    }

    public function findAll(LocatorInterface $locator): ElementCollection
    {
        return new ElementCollection($locator, $this->internalFindAll($locator));
    }

    private function internalFindAll(LocatorInterface $locator): Traversable
    {
        try {
            $elements = $this->waitUntil(function () use ($locator) {
                $elements = $this->decoratedElement->findAll($locator->getType(), $locator->getSelector());
                foreach ($elements as $element) {
                    if (!$element->isValid()) {
                        return false;
                    }
                }

                return $elements;
            }, '');
        } catch (TimeoutException $ex) {
            $elements = [];
        }

        foreach ($elements as $element) {
            $wrappedElement = new Element($locator, $element);
            $wrappedElement->setTimeout($this->timeout);

            if ($locator->elementMeetsCriteria($wrappedElement)) {
                yield $wrappedElement;
            }
        }
    }
}
