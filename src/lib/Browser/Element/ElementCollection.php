<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element;

use Ibexa\Behat\Browser\Assert\CollectionAssert;
use Ibexa\Behat\Browser\Element\Criterion\CriterionInterface;
use Ibexa\Behat\Browser\Element\Mapper\MapperInterface;
use Ibexa\Behat\Browser\Exception\ElementNotFoundException;
use Ibexa\Behat\Browser\Locator\LocatorInterface;
use PHPUnit\Framework\Assert;

class ElementCollection implements \Countable, \IteratorAggregate
{
    /** @var Element[]|\Traversable */
    private $elements;
    /**
     * @var \Ibexa\Behat\Browser\Locator\LocatorInterface
     */
    private $locator;

    public function __construct(LocatorInterface $locator, iterable $elements)
    {
        $this->elements = $elements;
        $this->locator = $locator;
    }

    /**
     * @return \Ibexa\Behat\Browser\Element\Element[]
     */
    public function getIterator(): iterable
    {
        if (is_array($this->elements)) {
            return new \ArrayIterator($this->elements);
        }

        return $this->elements;
    }

    public function getByCriterion(CriterionInterface $criterion): Element
    {
        foreach ($this->elements as $element) {
            if ($criterion->matches($element)) {
                return $element;
            }
        }

        throw new ElementNotFoundException(
            $criterion->getErrorMessage($this->locator)
        );
    }

    /**
     * @param callable Callable accepting a NodeElement parameter
     */
    public function getBy(callable $callable): Element
    {
        foreach ($this->elements as $element) {
            if ($callable($element)) {
                return $element;
            }
        }
    }

    public function first(): Element
    {
        foreach ($this->elements as $element) {
            return $element;
        }
    }

    public function last(): Element
    {
        if (!is_array($this->elements)) {
            $this->elements = iterator_to_array($this->elements);
        }

        return end($this->elements);
    }

    /**
     * @return \Ibexa\Behat\Browser\Element\Element[]
     */
    public function toArray(): array
    {
        if (!is_array($this->elements)) {
            $this->elements = iterator_to_array($this->elements);
        }

        return $this->elements;
    }

    public function count(): int
    {
        if (!is_array($this->elements)) {
            $this->elements = iterator_to_array($this->elements);
        }

        return count($this->elements);
    }

    public function any(): bool
    {
        if (!is_array($this->elements)) {
            $this->elements = iterator_to_array($this->elements);
        }

        return count($this->elements) > 0;
    }

    public function single(): Element
    {
        if (!is_array($this->elements)) {
            $this->elements = iterator_to_array($this->elements);
        }
        Assert::assertCount(
            1,
            $this->elements,
            sprintf(
                "Failed asserting that collection created with %s locator '%s': '%s' has only one element",
                $this->locator->getType(),
                $this->locator->getIdentifier(),
                $this->locator->getSelector()
            )
        );

        return $this->elements[0];
    }

    public function map(callable $callable): array
    {
        if (!is_array($this->elements)) {
            $this->elements = iterator_to_array($this->elements);
        }

        return array_map($callable, $this->elements);
    }

    public function mapBy(MapperInterface $mapper): array
    {
        $result = [];

        foreach ($this->elements as $element) {
            $result[] = $mapper->map($element);
        }

        return $result;
    }

    public function filter(callable $callable): self
    {
        return new ElementCollection($this->locator, $this->internalFilter($callable));
    }

    public function filterBy(CriterionInterface $criterion): self
    {
        return new ElementCollection($this->locator, $this->internalFilterBy($criterion));
    }

    public function empty(): bool
    {
        return 0 === count(iterator_to_array($this->elements));
    }

    public function assert(): CollectionAssert
    {
        return new CollectionAssert($this->locator, $this);
    }

    private function internalFilter(callable $callable): iterable
    {
        foreach ($this->elements as $element) {
            if ($callable($element)) {
                yield $element;
            }
        }
    }

    private function internalFilterBy(CriterionInterface $criterion): iterable
    {
        foreach ($this->elements as $element) {
            if ($criterion->matches($element)) {
                yield $element;
            }
        }
    }
}
