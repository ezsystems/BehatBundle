<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Behat\Browser\Element;

use Behat\Mink\Element\NodeElement;
use Ibexa\Behat\Browser\Element\Condition\ElementExistsCondition;
use Ibexa\Behat\Browser\Element\Element;
use Ibexa\Behat\Browser\Element\Factory\ElementFactory;
use Ibexa\Behat\Browser\Exception\TimeoutException;
use Ibexa\Behat\Browser\Locator\CSSLocator;
use Ibexa\Behat\Browser\Locator\LocatorInterface;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class ElementTest extends BaseTestCase
{
    /** @var \Ibexa\Behat\Browser\Locator\LocatorInterface */
    private $irrelevantLocator;

    /** @var \Ibexa\Behat\Browser\Locator\LocatorInterface */
    private $invalidLocator;

    /** @var \Behat\Mink\Session */
    private $session;

    public function setUp(): void
    {
        $this->irrelevantLocator = new CSSLocator('irrelevant-id', 'irrelevant-selector');
        $this->invalidLocator = new CSSLocator('invalid-id', 'invalid-selector');
    }

    public function testFindElementWhenExists(): void
    {
        $minkElement = $this->createMock(NodeElement::class);
        $childMinkElement = $this->createValidMinkNodeElement('Text');
        $minkElement->method('find')->willReturn($childMinkElement);
        $minkElement->method('findAll')->willReturn([$childMinkElement]);

        $element = $this->createElementWithMinkElement($minkElement);

        Assert::assertEquals('Text', $element->find($this->irrelevantLocator)->getText());
    }

    public function testFindElementWhenNotExists(): void
    {
        $minkElement = $this->createMock(NodeElement::class);
        $minkElement->method('find')->willReturn(null);
        $minkElement->method('findAll')->willReturn([]);
        $element = $this->createElementWithMinkElement($minkElement);

        $this->expectException(TimeoutException::class);
        $this->expectExceptionMessage("CSS selector 'invalid-id': 'invalid-selector' not found in 1 seconds.");
        $element->find($this->invalidLocator);
    }

    public function testFindAllWhenExist(): void
    {
        $minkElement = $this->createMock(NodeElement::class);
        $minkElement->method('findAll')->willReturn(
            [
                $this->createValidMinkNodeElement('Element1'),
                $this->createValidMinkNodeElement('Element2'),
            ]
        );
        $element = $this->createElementWithMinkElement($minkElement);

        Assert::assertCount(2, $element->findAll($this->irrelevantLocator));
    }

    public function testFindAllWhenNotExists(): void
    {
        $minkElement = $this->createMock(NodeElement::class);
        $minkElement->method('findAll')->willReturn([]);
        $element = $this->createElementWithMinkElement($minkElement);

        Assert::assertTrue($element->findAll($this->irrelevantLocator)->empty());
    }

    public function testWaitUntilWhenMet(): void
    {
        $minkElement = $this->createMock(NodeElement::class);
        $minkElement->method('findAll')->willReturn([]);
        $element = $this->createElementWithMinkElement($minkElement);

        $this->expectNotToPerformAssertions();
        $element->waitUntil(static function () {
            return true;
        }, 'Error message');
    }

    public function testWaitUntilWhenNotMet(): void
    {
        $minkElement = $this->createMock(NodeElement::class);
        $minkElement->method('findAll')->willReturn([]);
        $element = $this->createElementWithMinkElement($minkElement);

        $this->expectException(TimeoutException::class);
        $this->expectExceptionMessage('Custom error message');
        $element->waitUntil(static function () {
            return false;
        }, 'Custom error message');
    }

    public function testWaitUntilConditionWhenMet(): void
    {
        $minkElement = $this->createMock(NodeElement::class);
        $minkElement->method('findAll')->willReturn([$this->createValidMinkNodeElement('TestElement')]);
        $element = $this->createElementWithMinkElement($minkElement);

        $this->expectNotToPerformAssertions();
        $element->waitUntilCondition(new ElementExistsCondition($element, $this->irrelevantLocator));
    }

    public function testWaitUntilConditionWhenNotMet(): void
    {
        $minkElement = $this->createMock(NodeElement::class);
        $minkElement->method('findAll')->willReturn([]);
        $element = $this->createElementWithMinkElement($minkElement);
        $element->setTimeout(2);

        $searchedElement = $this->createElement('Test');
        $searchedElement->setTimeout(3);

        $this->expectException(TimeoutException::class);
        $element->waitUntilCondition(new ElementExistsCondition($searchedElement, $this->irrelevantLocator));
    }

    /**
     * @dataProvider dataProvidertestAdditionalLocatorConditionsAreAppliedWhenUsingFind
     */
    public function testAdditionalLocatorConditionsAreAppliedWhenUsingFind(LocatorInterface $locator, string $expectedElementText): void
    {
        $invisbleMinkElement = $this->createValidMinkNodeElement('InvisibleElement', false);
        $visibleMinkElement = $this->createValidMinkNodeElement('VisibleElement', true);

        $minkElement = $this->createMock(NodeElement::class);
        $minkElement->method('find')->willReturn($invisbleMinkElement);
        $minkElement->method('findAll')->willReturn([$invisbleMinkElement, $visibleMinkElement]);
        $element = $this->createElementWithMinkElement($minkElement);

        Assert::assertEquals($expectedElementText, $element->find($locator)->getText());
    }

    public function dataProvidertestAdditionalLocatorConditionsAreAppliedWhenUsingFind(): array
    {
        return [
            [new VisibleCSSLocator('id', 'selector'), 'VisibleElement'],
            [new CSSLocator('id', 'selector'), 'InvisibleElement'],
        ];
    }

    /**
     * @dataProvider dataProvidertestAdditionalLocatorConditionsAreAppliedWhenUsingFindAll
     */
    public function testAdditionalLocatorConditionsAreAppliedWhenUsingFindAll(LocatorInterface $locator, int $expectedElementCount): void
    {
        $invisbleMinkElement = $this->createValidMinkNodeElement('InvisibleElement', false);
        $visibleMinkElement = $this->createValidMinkNodeElement('VisibleElement', true);

        $minkElement = $this->createMock(NodeElement::class);
        $minkElement->method('findAll')->willReturn([$invisbleMinkElement, $visibleMinkElement]);
        $element = $this->createElementWithMinkElement($minkElement);

        Assert::assertCount($expectedElementCount, $element->findAll($locator));
    }

    public function dataProvidertestAdditionalLocatorConditionsAreAppliedWhenUsingFindAll(): array
    {
        return [
            [new VisibleCSSLocator('id', 'selector'), 1],
            [new CSSLocator('id', 'selector'), 2],
        ];
    }

    private function createElementWithMinkElement(NodeELement $nodeElement)
    {
        return new Element(new ElementFactory(), $this->irrelevantLocator, $nodeElement);
    }
}

class_alias(ElementTest::class, 'EzSystems\Behat\Test\Browser\Element\ElementTest');
