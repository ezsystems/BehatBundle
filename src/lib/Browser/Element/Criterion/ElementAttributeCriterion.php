<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Criterion;

use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Locator\LocatorInterface;

class ElementAttributeCriterion implements CriterionInterface
{
    /** @var string */
    private $expectedAttributeValue;

    /** @var array */
    private $results;

    /** @var string */
    private $attribute;

    public function __construct(string $attribute, string $expectedAttributeValue)
    {
        $this->attribute = $attribute;
        $this->expectedAttributeValue = $expectedAttributeValue;
        $this->results = [];
    }

    public function matches(ElementInterface $element): bool
    {
        $actualValue = $element->getAttribute($this->attribute);
        $this->results[] = $actualValue;

        return $actualValue === $this->expectedAttributeValue;
    }

    public function getErrorMessage(LocatorInterface $locator): string
    {
        return
            sprintf(
                "Could not find element with attribute '%s' matching value '%s'. Found values: %s instead. %s locator '%s': '%s'.",
                $this->attribute,
                $this->expectedAttributeValue,
                implode(',', $this->results),
                $locator->getType(),
                $locator->getIdentifier(),
                $locator->getSelector()
            );
    }
}
