<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Criterion;

use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Element\Mapper\ElementTextMapper;
use Ibexa\Behat\Browser\Locator\LocatorInterface;

class ChildElementTextCriterion implements CriterionInterface
{
    /**
     * @var string
     */
    private $expectedChildElementText;

    /**
     * @var \Ibexa\Behat\Browser\Locator\LocatorInterface
     */
    private $childLocator;

    /** @var array */
    private $results;

    public function __construct(LocatorInterface $childLocator, string $expectedChildElementText)
    {
        $this->expectedChildElementText = $expectedChildElementText;
        $this->childLocator = $childLocator;
        $this->results = [];
    }

    public function matches(ElementInterface $element): bool
    {
        $childElementsText = $element->findAll($this->childLocator)->mapBy(new ElementTextMapper());

        foreach ($childElementsText as $actualText) {
            if ($actualText === $this->expectedChildElementText) {
                return true;
            }
        }

        $this->results = array_merge($this->results, $childElementsText);

        return false;
    }

    public function getErrorMessage(LocatorInterface $locator): string
    {
        return
            sprintf(
                "Could not find element wih text: '%s' among children of given elements. Found names: %s instead. Parent %s locator '%s': '%s'. Child %s locator '%s': '%s'",
                $this->expectedChildElementText,
                implode(',', $this->results),
                $locator->getType(),
                $locator->getIdentifier(),
                $locator->getSelector(),
                $this->childLocator->getType(),
                $this->childLocator->getIdentifier(),
                $this->childLocator->getSelector()
            );
    }
}
