<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Test\Browser\Element;

use Behat\Mink\Element\NodeElement;
use Ibexa\Behat\Browser\Element\Condition\ElementExistsCondition;
use Ibexa\Behat\Browser\Element\Element;
use Ibexa\Behat\Browser\Exception\TimeoutException;
use Ibexa\Behat\Browser\Locator\CSSLocator;
use PHPUnit\Framework\Assert;

class ElementTest extends BaseTestCase
{
    /** @var \Ibexa\Behat\Browser\Element\Element */
    private $element;

    /** @var \Ibexa\Behat\Browser\Locator\LocatorInterface */
    private $irrelevantLocator;

    /** @var \Ibexa\Behat\Browser\Locator\LocatorInterface */
    private $validLocator;

    /** @var \Ibexa\Behat\Browser\Locator\LocatorInterface */
    private $invalidLocator;

    public function setUp(): void
    {
        $this->irrelevantLocator = new CSSLocator('irrelevant-id', 'irrelevant-selector');
        $this->validLocator = new CSSLocator('valid-id', 'valid-selector');
        $this->invalidLocator = new CSSLocator('invalid-id', 'invalid-selector');
    }

    public function testFindElementWhenExists(): void
    {
        $minkElement = $this->createMock(NodeElement::class);
        $minkElement->method('find')->willReturn($this->createValidMinkNodeElement('Text'));
        $this->element = new Element($this->irrelevantLocator, $minkElement);

        Assert::assertEquals('Text', $this->element->find($this->irrelevantLocator)->getText());
    }

    public function testFindElementWhenNotExists(): void
    {
        $minkElement = $this->createMock(NodeElement::class);
        $minkElement->method('find')->willReturn(null);
        $element = new Element($this->irrelevantLocator, $minkElement);

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
        $element = new Element($this->irrelevantLocator, $minkElement);

        Assert::assertCount(2, $element->findAll($this->irrelevantLocator));
    }

    public function testFindAllWhenNotExists(): void
    {
        $minkElement = $this->createMock(NodeElement::class);
        $minkElement->method('findAll')->willReturn([]);
        $element = new Element($this->irrelevantLocator, $minkElement);

        Assert::assertTrue($element->findAll($this->irrelevantLocator)->empty());
    }

    public function testWaitUntilWhenMet(): void
    {
        $minkElement = $this->createMock(NodeElement::class);
        $minkElement->method('findAll')->willReturn([]);
        $element = new Element($this->irrelevantLocator, $minkElement);

        $this->expectNotToPerformAssertions();
        $element->waitUntil(static function () {
            return true;
        }, 'Error message');
    }

    public function testWaitUntilWhenNotMet(): void
    {
        $minkElement = $this->createMock(NodeElement::class);
        $minkElement->method('findAll')->willReturn([]);
        $element = new Element($this->irrelevantLocator, $minkElement);

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
        $element = new Element($this->irrelevantLocator, $minkElement);

        $this->expectNotToPerformAssertions();
        $element->waitUntilCondition(new ElementExistsCondition($element, $this->irrelevantLocator));
    }

    public function testWaitUntilConditionWhenNotMet(): void
    {
        $minkElement = $this->createMock(NodeElement::class);
        $minkElement->method('findAll')->willReturn([]);
        $element = new Element($this->irrelevantLocator, $minkElement);
        $element->setTimeout(2);

        $searchedElement = $this->createElement('Test');
        $searchedElement->setTimeout(3);

        $this->expectException(TimeoutException::class);
        $element->waitUntilCondition(new ElementExistsCondition($searchedElement, $this->irrelevantLocator));
    }
}
