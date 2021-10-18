<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Test\Browser\Element\Criterion;

use EzSystems\Behat\Test\Browser\Element\BaseTestCase;
use Ibexa\Behat\Browser\Element\Criterion\ChildElementTextCriterion;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Locator\CSSLocator;
use Ibexa\Behat\Browser\Locator\XPathLocator;
use PHPUnit\Framework\Assert;

class ChildElementTextCriterionTest extends BaseTestCase
{
    /**
     * @dataProvider dataProviderTestMatches
     */
    public function testMatches(ElementInterface $element, bool $shouldMatch): void
    {
        $criterion = new ChildElementTextCriterion(new XPathLocator('id', 'selector'), 'expectedChildText');

        Assert::assertEquals($shouldMatch, $criterion->matches($element));
    }

    public function dataProviderTestMatches(): array
    {
        return [
            [$this->createElementWithChildElement('ignore', new XPathLocator('id', 'selector'), $this->createElement('expectedChildText')), true],
            [$this->createElementWithChildElement('ignore', new XPathLocator('id', 'selector'), $this->createElement('notExpectedChildText')), false],
            [$this->createElementWithChildElement('ignore', new XPathLocator('id', 'invalidSelector'), $this->createElement('expectedChildText')), false],
        ];
    }

    public function testGetErrorMessageWhenNoElementFound(): void
    {
        $childLocator = new XPathLocator('id', 'child-selector');
        $criterion = new ChildElementTextCriterion($childLocator, 'expectedChildText');
        $invalidElement = $this->createElementWithChildElement('ignore', $childLocator, $this->createElement('expectedChildText'));
        $criterion->matches($invalidElement);

        Assert::assertEquals(
            "Could not find element wih text: 'expectedChildText' among children of given elements.
                No element matching given child locator found.
                Parent CSS locator 'parent-id': 'parent-selector'.
                Child XPATH locator 'id': 'child-selector'.",
            $criterion->getErrorMessage(new CSSLocator('parent-id', 'parent-selector'))
        );
    }

    public function testGetErrorMessageWhenOtherElementFound(): void
    {
        $childLocator = new XPathLocator('id', 'child-selector');
        $criterion = new ChildElementTextCriterion($childLocator, 'expectedChildText');
        $invalidElement = $this->createElementWithChildElement('ignore', $childLocator, $this->createElement('notExpectedChildText'));
        $criterion->matches($invalidElement);

        Assert::assertEquals(
            "Could not find element wih text: 'expectedChildText' among children of given elements.
                Found names: 'notExpectedChildText' instead.
                Parent CSS locator 'parent-id': 'parent-selector'.
                Child XPATH locator 'id': 'child-selector'.",
            $criterion->getErrorMessage(new CSSLocator('parent-id', 'parent-selector'))
        );
    }
}
