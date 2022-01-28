<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Assert;

use Ibexa\Behat\Browser\Element\ElementCollectionInterface;
use Ibexa\Behat\Browser\Locator\LocatorInterface;
use PHPUnit\Framework\Assert;

class CollectionAssert implements CollectionAssertInterface
{
    /**
     * @var \Ibexa\Behat\Browser\Locator\LocatorInterface
     */
    private $locator;

    /**
     * @var \Ibexa\Behat\Browser\Element\ElementCollectionInterface
     */
    private $elementCollection;

    public function __construct(LocatorInterface $locator, ElementCollectionInterface $elementCollection)
    {
        $this->locator = $locator;
        $this->elementCollection = $elementCollection;
    }

    public function isEmpty(): ElementCollectionInterface
    {
        Assert::assertTrue(
            $this->elementCollection->empty(),
            sprintf(
                "Failed asserting that Collection created with %s locator '%s': '%s' is empty",
                $this->locator->getType(),
                $this->locator->getIdentifier(),
                $this->locator->getSelector()
            )
        );

        return $this->elementCollection;
    }

    public function hasElements(): ElementCollectionInterface
    {
        $elements = $this->elementCollection->toArray();
        $this->elementCollection->setElements($elements);

        Assert::assertNotEmpty(
            $elements,
            sprintf(
                "Failed asserting that Collection created with %s locator '%s': '%s' is not empty",
                $this->locator->getType(),
                $this->locator->getIdentifier(),
                $this->locator->getSelector()
            )
        );

        return $this->elementCollection;
    }

    public function countEquals(int $expectedCount): ElementCollectionInterface
    {
        $elements = $this->elementCollection->toArray();
        $this->elementCollection->setElements($elements);

        Assert::assertCount(
            $expectedCount,
            $elements,
            sprintf(
                "Failed asserting that Collection created with %s locator '%s': '%s' has %d elements. Found %s instead.",
                $this->locator->getType(),
                $this->locator->getIdentifier(),
                $this->locator->getSelector(),
                $expectedCount,
                count($elements)
            )
        );

        return $this->elementCollection;
    }
}
