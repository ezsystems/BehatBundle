<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Test\Browser\Element\Criterion;

use EzSystems\Behat\Test\Browser\Element\BaseTestCase;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Locator\CSSLocator;
use PHPUnit\Framework\Assert;

class ElementTextCriterionTest extends BaseTestCase
{
    /**
     * @dataProvider dataProviderTestMatches
     */
    public function testMatches(ElementInterface $element, bool $shouldMatch): void
    {
        $criterion = new ElementTextCriterion('expectedText');

        Assert::assertEquals($shouldMatch, $criterion->matches($element));
    }

    public function dataProviderTestMatches(): array
    {
        return [
            [$this->createElement('expectedText'), true],
            [$this->createElement('notExpectedChildText'), false],
        ];
    }

    public function testGetErrorMessage(): void
    {
        $criterion = new ElementTextCriterion('expectedText');
        $nonMatchingElement = $this->createElement('actualText');
        $criterion->matches($nonMatchingElement);

        Assert::assertEquals(
            "Could not find element named: 'expectedText'. Found names: actualText instead. CSS locator 'id': 'selector'.",
            $criterion->getErrorMessage(new CSSLocator('id', 'selector'))
        );
    }
}
