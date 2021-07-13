<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Assert;

use Ibexa\Behat\Browser\Element\ElementCollection;
use Ibexa\Behat\Browser\Locator\LocatorInterface;
use PHPUnit\Framework\Assert;

class CollectionAssert
{
    /**
     * @var \Ibexa\Behat\Browser\Locator\LocatorInterface
     */
    private $locator;

    /**
     * @var \Ibexa\Behat\Browser\Element\ElementCollection
     */
    private $elementCollection;

    public function __construct(LocatorInterface $locator, ElementCollection $elementCollection)
    {
        $this->locator = $locator;
        $this->elementCollection = $elementCollection;
    }

    public function isEmpty(): ElementCollection
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

    public function hasElements(): ElementCollection
    {
        $elements = $this->elementCollection->toArray();

        Assert::assertNotEmpty(
            $elements,
            sprintf(
                "Failed asserting that Collection created with %s locator '%s': '%s' is not empty",
                $this->locator->getType(),
                $this->locator->getIdentifier(),
                $this->locator->getSelector()
            )
        );

        return new ElementCollection($this->locator, $elements);
    }

    public function countEquals(int $expectedCount): ElementCollection
    {
        $elements = $this->elementCollection->toArray();
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

        return new ElementCollection($this->locator, $elements);
    }
}
