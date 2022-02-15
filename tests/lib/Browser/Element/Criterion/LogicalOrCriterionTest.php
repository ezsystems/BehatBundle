<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Behat\Browser\Element\Criterion;

use Ibexa\Tests\Behat\Browser\Element\BaseTestCase;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Element\Criterion\LogicalOrCriterion;
use Ibexa\Behat\Browser\Locator\CSSLocator;
use PHPUnit\Framework\Assert;

class LogicalOrCriterionTest extends BaseTestCase
{
    public function testMatchesWhenSingleCriterionMatches(): void
    {
        $criterions = [
            new ElementTextCriterion('Test1'),
            new ElementTextCriterion('Test2'),
        ];
        $matchingElement = $this->createElement('Test2');

        $testedCriterion = new LogicalOrCriterion($criterions);

        Assert::assertTrue($testedCriterion->matches($matchingElement));
    }

    public function testNoMatchWhenNoCriterionMatches(): void
    {
        $testedCriterion = new LogicalOrCriterion();
        $testedCriterion->addCriterion(new ElementTextCriterion('Test1'));
        $testedCriterion->addCriterion(new ElementTextCriterion('Test2'));

        $nonmatchingElement = $this->createElement('Test3');

        Assert::assertFalse($testedCriterion->matches($nonmatchingElement));
        Assert::assertEquals(
            "LogicalOr criterion failed. Condition error messages:
Could not find element named: 'Test1'. Found names: Test3 instead. CSS locator 'id': 'selector'.
Could not find element named: 'Test2'. Found names: Test3 instead. CSS locator 'id': 'selector'.
",
            $testedCriterion->getErrorMessage(new CSSLocator('id', 'selector'))
        );
    }
}

class_alias(LogicalOrCriterionTest::class, 'EzSystems\Behat\Test\Browser\Element\Criterion\LogicalOrCriterionTest');
