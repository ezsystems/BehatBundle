<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element;

use Ibexa\Behat\Browser\Assert\CollectionAssertInterface;
use Ibexa\Behat\Browser\Element\Criterion\CriterionInterface;
use Ibexa\Behat\Browser\Element\Mapper\MapperInterface;

interface ElementCollectionInterface extends \Countable, \IteratorAggregate
{
    /**
     * @return \Ibexa\Behat\Browser\Element\ElementInterface[]
     */
    public function getIterator(): iterable;

    public function setElements(array $elements): void;

    public function getByCriterion(CriterionInterface $criterion): ElementInterface;

    /**
     * @param callable Callable accepting a NodeElement parameter
     */
    public function getBy(callable $callable): ElementInterface;

    public function first(): ElementInterface;

    public function last(): ElementInterface;

    /**
     * @return \Ibexa\Behat\Browser\Element\ElementInterface[]
     */
    public function toArray(): array;

    public function count(): int;

    public function any(): bool;

    public function single(): ElementInterface;

    public function map(callable $callable): array;

    public function mapBy(MapperInterface $mapper): array;

    public function filter(callable $callable): ElementCollectionInterface;

    public function filterBy(CriterionInterface $criterion): ElementCollectionInterface;

    public function empty(): bool;

    public function assert(): CollectionAssertInterface;
}
