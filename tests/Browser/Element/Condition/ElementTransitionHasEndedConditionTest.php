<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Test\Browser\Element\Condition;

use EzSystems\Behat\Test\Browser\Element\BaseTestCase;
use Ibexa\Behat\Browser\Element\Condition\ElementTransitionHasEndedCondition;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Locator\CSSLocator;
use PHPUnit\Framework\Assert;

class ElementTransitionHasEndedConditionTest extends BaseTestCase
{
    public function testElementTransitionHasEnded(): void
    {
        $searchedElementLocator = new CSSLocator('searched-id', 'searched-test');

        $condition = new ElementTransitionHasEndedCondition(
            $this->createElementWithChildElement('root', $searchedElementLocator, $this->createChildElement(false, true)),
            $searchedElementLocator
        );

        Assert::assertTrue($condition->isMet());
    }

    public function testElementTransitionHasNotEndedInTime(): void
    {
        $searchedElementLocator = new CSSLocator('searched-id', 'searched-test');
        $baseElement = $this->createElementWithChildElement('root', $searchedElementLocator, $this->createElement('ChildText'));

        $condition = new ElementTransitionHasEndedCondition(
            $this->createElementWithChildElement('root', $searchedElementLocator, $this->createChildElement(true, false)),
            $searchedElementLocator
        );

        Assert::assertFalse($condition->isMet());
        Assert::assertEquals(
            "Transition has not ended for element with CSS locator 'searched-id': 'searched-test'. Timeout value: 1 seconds.",
            $condition->getErrorMessage($baseElement)
        );
    }

    public function testElementTransitionHasNotStarted(): void
    {
        $searchedElementLocator = new CSSLocator('searched-id', 'searched-test');
        $baseElement = $this->createElementWithChildElement('root', $searchedElementLocator, $this->createElement('ChildText'));

        $condition = new ElementTransitionHasEndedCondition(
            $this->createElementWithChildElement('root', $searchedElementLocator, $this->createChildElement(false, false)),
            $searchedElementLocator
        );

        Assert::assertFalse($condition->isMet());
        Assert::assertEquals(
            "Transition has not started at all for element with CSS locator 'searched-id': 'searched-test'. Please make sure the condition is used on the correct element. Timeout value: 1 seconds.",
            $condition->getErrorMessage($baseElement)
        );
    }

    private function createChildElement(bool $hasStartedTransition, bool $hasEndedTransition): ElementInterface
    {
        $childElement = $this->createStub(ElementInterface::class);
        $childElement->method('getText')->willReturn('ChildText');
        $childElement->method('hasClass')->will($this->returnValueMap(
            [
                ['ibexa-selenium-transition-started', $hasStartedTransition],
                ['ibexa-selenium-transition-ended', $hasEndedTransition],
            ]
        ));

        return $childElement;
    }
}
