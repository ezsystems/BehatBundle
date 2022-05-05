<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Assert\Debug\Interactive;

use EzSystems\Behat\Core\Debug\InteractiveDebuggerTrait;
use Ibexa\Behat\Browser\Assert\CollectionAssertInterface;
use Ibexa\Behat\Browser\Element\ElementCollectionInterface;
use PHPUnit\Framework\ExpectationFailedException;

class CollectionAssert implements CollectionAssertInterface
{
    use InteractiveDebuggerTrait;

    /** @var \Ibexa\Behat\Browser\Assert\CollectionAssertInterface */
    private $baseCollectionAssert;

    public function __construct(CollectionAssertInterface $baseCollectionAssert)
    {
        $this->baseCollectionAssert = $baseCollectionAssert;
    }

    public function isEmpty(): ElementCollectionInterface
    {
        try {
            return $this->baseCollectionAssert->isEmpty();
        } catch (ExpectationFailedException $exception) {
            return $this->startInteractiveSessionOnException($exception, true);
        }
    }

    public function hasElements(): ElementCollectionInterface
    {
        try {
            return $this->baseCollectionAssert->hasElements();
        } catch (ExpectationFailedException $exception) {
            return $this->startInteractiveSessionOnException($exception, true);
        }
    }

    public function countEquals(int $expectedCount): ElementCollectionInterface
    {
        try {
            return $this->baseCollectionAssert->countEquals($expectedCount);
        } catch (ExpectationFailedException $exception) {
            return $this->startInteractiveSessionOnException($exception, true);
        }
    }

    public function containsElementsWithText(array $expectedElementTexts): ElementCollectionInterface
    {
        try {
            return $this->baseCollectionAssert->containsElementsWithText($expectedElementTexts);
        } catch (ExpectationFailedException $exception) {
            return $this->startInteractiveSessionOnException($exception, true);
        }
    }
}
