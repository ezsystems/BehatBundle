<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element;

use Ibexa\Behat\Browser\Element\Condition\ConditionInterface;
use Ibexa\Behat\Browser\Element\Factory\ElementFactoryInterface;
use Ibexa\Behat\Browser\Exception\TimeoutException;
use Ibexa\Behat\Browser\Locator\LocatorInterface;
use Traversable;

class BaseElement implements BaseElementInterface
{
    /** @var int */
    protected $timeout = 1;

    /** @var \Behat\Mink\Element\TraversableElement */
    protected $decoratedElement;

    /** @var \Ibexa\Behat\Browser\Component\ElementFactoryInterface */
    private $elementFactory;

    public function __construct(ElementFactoryInterface $elementFactory)
    {
        $this->elementFactory = $elementFactory;
    }

    public function setTimeout(int $timeoutSeconds): BaseElementInterface
    {
        $this->timeout = $timeoutSeconds;

        return $this;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
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

        throw new TimeoutException($condition->getErrorMessage($this));
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
                $foundMinkElements = $this->decoratedElement->findAll($locator->getType(), $locator->getSelector());

                foreach ($foundMinkElements as $foundMinkElement) {
                    $wrappedElement = $this->elementFactory->createElement($this->elementFactory, $locator, $foundMinkElement);

                    if ($locator->elementMeetsCriteria($wrappedElement)) {
                        return $wrappedElement;
                    }
                }

                return false;
            },
            sprintf(
                "%s selector '%s': '%s' not found in %d seconds.",
                strtoupper($locator->getType()),
                $locator->getIdentifier(),
                $locator->getSelector(),
                $this->timeout
            )
        );
    }

    public function findAll(LocatorInterface $locator): ElementCollectionInterface
    {
        return new ElementCollection($locator, $this->internalFindAll($locator));
    }

    private function internalFindAll(LocatorInterface $locator): Traversable
    {
        try {
            $minkElements = $this->waitUntil(function () use ($locator) {
                $minkElements = $this->decoratedElement->findAll($locator->getType(), $locator->getSelector());
                foreach ($minkElements as $minkElement) {
                    if (!$minkElement->isValid()) {
                        return false;
                    }
                }

                return $minkElements;
            }, '');
        } catch (TimeoutException $ex) {
            $minkElements = [];
        }

        foreach ($minkElements as $minkElement) {
            $wrappedElement = $this->elementFactory->createElement($this->elementFactory, $locator, $minkElement);
            $wrappedElement->setTimeout($this->timeout);

            if ($locator->elementMeetsCriteria($wrappedElement)) {
                yield $wrappedElement;
            }
        }
    }
}
