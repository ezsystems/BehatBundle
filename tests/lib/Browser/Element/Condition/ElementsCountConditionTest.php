<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Behat\Browser\Element\Condition;

use Ibexa\Tests\Behat\Browser\Element\BaseTestCase;
use Ibexa\Behat\Browser\Element\Condition\ElementsCountCondition;
use Ibexa\Behat\Browser\Locator\CSSLocator;
use PHPUnit\Framework\Assert;

class ElementsCountConditionTest extends BaseTestCase
{
    public function testExpectedCountPresent(): void
    {
        $searchedElementLocator = new CSSLocator('searched-id', 'searched-test');
        $condition = new ElementsCountCondition(
            $this->createElementWithChildElement('root', $searchedElementLocator, $this->createElement('ChildText')),
            $searchedElementLocator,
            1
        );

        Assert::assertTrue($condition->isMet());
    }

    public function testExpectedCountNotPresent(): void
    {
        $searchedElementLocator = new CSSLocator('searched-id', 'searched-test');
        $condition = new ElementsCountCondition(
            $this->createElementWithChildElement('root', $searchedElementLocator, $this->createElement('ChildText')),
            $searchedElementLocator,
            0
        );
        $invokingElement = $this->createElement('Test');
        $invokingElement->method('getTimeout')->willReturn(5);

        Assert::assertFalse($condition->isMet());
        Assert::assertEquals(
            "The expected number of items (0) matching CSS locator 'searched-id': 'searched-test' was not found. Found 1 items instead. Timeout value: 5 seconds.",
            $condition->getErrorMessage($invokingElement)
        );
    }
}

class_alias(ElementsCountConditionTest::class, 'EzSystems\Behat\Test\Browser\Element\Condition\ElementsCountConditionTest');
