<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Debug\Interactive;

use Ibexa\Behat\Core\Debug\InteractiveDebuggerTrait;
use Ibexa\Behat\Browser\Element\BaseElementInterface;
use Ibexa\Behat\Browser\Element\Condition\ConditionInterface;
use Ibexa\Behat\Browser\Element\ElementCollectionInterface;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Exception\TimeoutException;
use Ibexa\Behat\Browser\Locator\LocatorInterface;

class BaseElement implements BaseElementInterface
{
    use InteractiveDebuggerTrait;

    /** @var \Ibexa\Behat\Browser\Element\BaseElementInterface */
    protected $element;

    public function __construct(BaseElementInterface $element)
    {
        $this->element = $element;
    }

    public function setTimeout(int $timeoutSeconds): BaseElementInterface
    {
        $this->element->setTimeout($timeoutSeconds);

        return $this;
    }

    public function getTimeout(): int
    {
        return $this->element->getTimeout();
    }

    public function find(LocatorInterface $locator): ElementInterface
    {
        try {
            return $this->element->find($locator);
        } catch (TimeoutException $exception) {
            return $this->startInteractiveSessionOnException($exception, true);
        }
    }

    public function findAll(LocatorInterface $locator): ElementCollectionInterface
    {
        $elements = $this->element->findAll($locator);

        return new ElementCollection($elements);
    }

    public function waitUntilCondition(ConditionInterface $condition): BaseElementInterface
    {
        try {
            $this->element->waitUntilCondition($condition);
        } catch (TimeoutException $exception) {
            $this->startInteractiveSessionOnException($exception, false);
        }

        return $this;
    }

    public function waitUntil(callable $callback, string $errorMessage)
    {
        return $this->element->waitUntil($callback, $errorMessage);
    }
}
