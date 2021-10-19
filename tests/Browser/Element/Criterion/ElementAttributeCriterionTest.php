<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Test\Browser\Element\Criterion;

use EzSystems\Behat\Test\Browser\Element\BaseTestCase;
use Ibexa\Behat\Browser\Element\Criterion\ElementAttributeCriterion;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Locator\CSSLocator;
use PHPUnit\Framework\Assert;

class ElementAttributeCriterionTest extends BaseTestCase
{
    /**
     * @dataProvider dataProviderTestMatches
     */
    public function testMatches(ElementInterface $element, bool $shouldMatch): void
    {
        $criterion = new ElementAttributeCriterion('expectedAttribute', 'expectedValue');

        Assert::assertEquals($shouldMatch, $criterion->matches($element));
    }

    public function dataProviderTestMatches(): array
    {
        return [
            [$this->createElementWithAttribute('expectedAttribute', ''), false],
            [$this->createElementWithAttribute('expectedAttribute', 'notexpectedValue'), false],
            [$this->createElementWithAttribute('expectedAttribute', 'expectedValue'), true],
        ];
    }

    public function testGetErrorMessageWhenOtherElementFound(): void
    {
        $criterion = new ElementAttributeCriterion('expectedAttribute', 'expectedValue');
        $nonMatchingElement = $this->createElementWithAttribute('expectedAttribute', 'notexpectedValue');
        $criterion->matches($nonMatchingElement);

        Assert::assertEquals(
            "Could not find element with attribute 'expectedAttribute' matching value 'expectedValue'. Found values: notexpectedValue instead. css locator 'id': 'selector'.",
            $criterion->getErrorMessage(new CSSLocator('id', 'selector'))
        );
    }

    public function testGetErrorMessageWhenNoElementFound(): void
    {
        $criterion = new ElementAttributeCriterion('expectedAttribute', 'expectedValue');

        Assert::assertEquals(
            "Could not find element with attribute 'expectedAttribute' matching value 'expectedValue'. Collection is empty. css locator 'id': 'selector'.",
            $criterion->getErrorMessage(new CSSLocator('id', 'selector'))
        );
    }

    private function createElementWithAttribute($attribute, $value): ElementInterface
    {
        $element = $this->createStub(ElementInterface::class);
        $element->method('getAttribute')->with($attribute)->willReturn($value);

        return $element;
    }
}
