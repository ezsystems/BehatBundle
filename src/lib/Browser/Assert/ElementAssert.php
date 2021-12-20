<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Assert;

use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Locator\LocatorInterface;
use PHPUnit\Framework\Assert;

class ElementAssert implements ElementAssertInterface
{
    /**
     * @var \Ibexa\Behat\Browser\Locator\LocatorInterface
     */
    private $locator;
    /**
     * @var \Ibexa\Behat\Browser\Element\ElementInterface
     */
    private $element;

    public function __construct(LocatorInterface $locator, ElementInterface $element)
    {
        $this->locator = $locator;
        $this->element = $element;
    }

    public function textEquals(string $expectedText): ElementInterface
    {
        $actualText = $this->element->getText();

        Assert::assertEquals(
            $expectedText,
            $actualText,
            sprintf(
                "Failed asserting that expected string '%s' is equal to actual '%s' for %s locator '%s': '%s'",
                $expectedText,
                $actualText,
                $this->locator->getType(),
                $this->locator->getIdentifier(),
                $this->locator->getSelector()
            )
        );

        return $this->element;
    }

    public function textContains(string $expectedTextFragment): ElementInterface
    {
        $actualText = $this->element->getText();

        Assert::assertStringContainsString(
            $expectedTextFragment,
            $actualText,
            sprintf(
                "Failed asserting that expected text '%s' is found in actual text '%s' for %s locator '%s': '%s'",
                $expectedTextFragment,
                $actualText,
                $this->locator->getType(),
                $this->locator->getIdentifier(),
                $this->locator->getSelector()
            )
        );

        return $this->element;
    }

    public function isVisible(): ElementInterface
    {
        Assert::assertTrue(
            $this->element->isVisible(),
            sprintf(
                "Failed asserting that %s locator '%s': '%s' is visible",
                $this->locator->getType(),
                $this->locator->getIdentifier(),
                $this->locator->getSelector()
            )
        );

        return $this->element;
    }

    public function hasClass(string $expectedClass): ElementInterface
    {
        $actualClass = $this->element->getAttribute('class');

        Assert::assertTrue($this->element->hasClass($expectedClass),
            sprintf(
                "Failed asserting that element with %s locator '%s': '%s' has '%s' class, instead class is '%s'",
                $this->locator->getType(),
                $this->locator->getIdentifier(),
                $this->locator->getSelector(),
                $expectedClass,
                $actualClass
            )
        );

        return $this->element;
    }
}
