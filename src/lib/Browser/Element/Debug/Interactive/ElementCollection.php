<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Debug\Interactive;

use EzSystems\Behat\Core\Debug\InteractiveDebuggerTrait;
use Ibexa\Behat\Browser\Assert\CollectionAssertInterface;
use Ibexa\Behat\Browser\Assert\Debug\Interactive\CollectionAssert as InteractiveElementCollectionAssert;
use Ibexa\Behat\Browser\Element\Criterion\CriterionInterface;
use Ibexa\Behat\Browser\Element\ElementCollectionInterface;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Element\Mapper\MapperInterface;
use Ibexa\Behat\Browser\Exception\ElementNotFoundException;
use PHPUnit\Framework\ExpectationFailedException;

class ElementCollection implements ElementCollectionInterface
{
    use InteractiveDebuggerTrait;

    private $baseElementCollection;

    public function __construct(ElementCollectionInterface $baseElementCollection)
    {
        $this->baseElementCollection = $baseElementCollection;
    }

    public function setElements(array $elements): void
    {
        $this->baseElementCollection->setElements($elements);
    }

    public function getIterator(): iterable
    {
        return $this->baseElementCollection->getIterator();
    }

    public function getByCriterion(CriterionInterface $criterion): ElementInterface
    {
        try {
            return $this->baseElementCollection->getByCriterion($criterion);
        } catch (ElementNotFoundException $exception) {
            return $this->startInteractiveSessionOnException($exception, true);
        }
    }

    public function getBy(callable $callable): ElementInterface
    {
        try {
            return $this->baseElementCollection->getBy($callable);
        } catch (ElementNotFoundException $exception) {
            return $this->startInteractiveSessionOnException($exception, true);
        }
    }

    public function first(): ElementInterface
    {
        try {
            return $this->baseElementCollection->first();
        } catch (ElementNotFoundException $exception) {
            return $this->startInteractiveSessionOnException($exception, true);
        }
    }

    public function last(): ElementInterface
    {
        try {
            return $this->baseElementCollection->last();
        } catch (ElementNotFoundException $exception) {
            return $this->startInteractiveSessionOnException($exception, true);
        }
    }

    public function toArray(): array
    {
        return $this->baseElementCollection->toArray();
    }

    public function count(): int
    {
        return $this->baseElementCollection->count();
    }

    public function any(): bool
    {
        return $this->baseElementCollection->any();
    }

    public function single(): ElementInterface
    {
        try {
            return $this->baseElementCollection->single();
        } catch (ExpectationFailedException $exception) {
            return $this->startInteractiveSessionOnException($exception, true);
        }
    }

    public function map(callable $callable): array
    {
        return $this->baseElementCollection->map($callable);
    }

    public function mapBy(MapperInterface $mapper): array
    {
        return $this->baseElementCollection->mapBy($mapper);
    }

    public function filter(callable $callable): ElementCollectionInterface
    {
        return new ElementCollection($this->baseElementCollection->filter($callable));
    }

    public function filterBy(CriterionInterface $criterion): ElementCollectionInterface
    {
        return new ElementCollection($this->baseElementCollection->filterBy($criterion));
    }

    public function empty(): bool
    {
        return $this->baseElementCollection->empty();
    }

    public function assert(): CollectionAssertInterface
    {
        return new InteractiveElementCollectionAssert($this->baseElementCollection->assert());
    }
}
