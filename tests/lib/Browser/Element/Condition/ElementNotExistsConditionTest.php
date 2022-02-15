<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Behat\Browser\Element\Condition;

use Ibexa\Tests\Behat\Browser\Element\BaseTestCase;
use Ibexa\Behat\Browser\Element\Condition\ElementNotExistsCondition;
use Ibexa\Behat\Browser\Locator\CSSLocator;
use PHPUnit\Framework\Assert;

class ElementNotExistsConditionTest extends BaseTestCase
{
    public function testElementDoesNotExist(): void
    {
        $searchedElementLocator = new CSSLocator('not-exist-id', 'not-exist-selector');
        $condition = new ElementNotExistsCondition(
            $this->createElementWithChildElement(
                'root',
                new CSSLocator('dummy-id', 'dummy-selector'),
                $this->createElement('DummyText')
            ),
            $searchedElementLocator
        );

        Assert::assertTrue($condition->isMet());
    }

    public function testElementExist(): void
    {
        $shouldNotExistLocator = new CSSLocator('should-not-exist-id', 'should-not-exist-selector');
        $condition = new ElementNotExistsCondition(
            $this->createElementWithChildElement('root', $shouldNotExistLocator, $this->createElement('ChildText')),
            $shouldNotExistLocator
        );
        $invokingElement = $this->createElement('Test');
        $invokingElement->method('getTimeout')->willReturn(5);

        Assert::assertFalse($condition->isMet());
        Assert::assertEquals(
            "Element with CSS locator 'should-not-exist-id': 'should-not-exist-selector' was found, but it should not exist. Timeout value: 5 seconds.",
            $condition->getErrorMessage($invokingElement)
        );
    }
}

class_alias(ElementNotExistsConditionTest::class, 'EzSystems\Behat\Test\Browser\Element\Condition\ElementNotExistsConditionTest');
