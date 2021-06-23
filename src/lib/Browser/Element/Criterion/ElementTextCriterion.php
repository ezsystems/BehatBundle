<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Criterion;

use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Locator\LocatorInterface;

class ElementTextCriterion implements CriterionInterface
{
    /**
     * @var string
     */
    private $expectedElementText;

    /** @var array */
    private $results;

    public function __construct(string $expectedElementText)
    {
        $this->expectedElementText = $expectedElementText;
        $this->results = [];
    }

    public function matches(ElementInterface $element): bool
    {
        $actualValue = $element->getText();
        $this->results[] = $actualValue;

        return $actualValue === $this->expectedElementText;
    }

    public function getErrorMessage(LocatorInterface $locator): string
    {
        return
            sprintf(
                "Could not find element named: '%s'. Found names: %s instead. %s locator '%s': '%s'",
                $this->expectedElementText,
                implode(',', $this->results),
                $locator->getType(),
                $locator->getIdentifier(),
                $locator->getSelector()
            );
    }
}
