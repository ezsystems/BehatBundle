<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Behat\Browser\Element\Condition;

use Ibexa\Tests\Behat\Browser\Element\BaseTestCase;
use Ibexa\Behat\Browser\Element\Condition\ElementExistsCondition;
use Ibexa\Behat\Browser\Locator\CSSLocator;
use PHPUnit\Framework\Assert;

class ElementExistsConditionTest extends BaseTestCase
{
    public function testElementExists(): void
    {
        $searchedElementLocator = new CSSLocator('searched-id', 'searched-test');
        $condition = new ElementExistsCondition(
            $this->createElementWithChildElement('root', $searchedElementLocator, $this->createElement('ChildText')),
            $searchedElementLocator
        );

        Assert::assertTrue($condition->isMet());
    }

    public function testElementDoesNotExist(): void
    {
        $condition = new ElementExistsCondition(
            $this->createElementWithChildElement('root', new CSSLocator('irrelevant-id', 'irrelevant-child'), $this->createElement('ChildText')),
            new CSSLocator('searched-id', 'searched-test')
        );

        $invokingElement = $this->createElement('Test');
        $invokingElement->method('getTimeout')->willReturn(5);

        Assert::assertFalse($condition->isMet());
        Assert::assertEquals(
            "Element with CSS locator 'searched-id': 'searched-test' was not found. Timeout value: 5 seconds.",
            $condition->getErrorMessage($invokingElement)
        );
    }
}

class_alias(ElementExistsConditionTest::class, 'EzSystems\Behat\Test\Browser\Element\Condition\ElementExistsConditionTest');
