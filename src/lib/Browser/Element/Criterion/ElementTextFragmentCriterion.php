<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Criterion;

use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Locator\LocatorInterface;

class ElementTextFragmentCriterion implements CriterionInterface
{
    /**
     * @var string
     */
    private $expectedElementTextFragment;

    /** @var array */
    private $results;

    public function __construct(string $expectedElementTextFragment)
    {
        $this->expectedElementTextFragment = $expectedElementTextFragment;
        $this->results = [];
    }

    public function matches(ElementInterface $element): bool
    {
        $actualValue = $element->getText();
        $this->results[] = $actualValue;

        return false !== strpos($actualValue, $this->expectedElementTextFragment);
    }

    public function getErrorMessage(LocatorInterface $locator): string
    {
        return
            $this->results ?
                sprintf(
                    "Could not find element with text containing: '%s'. Found texts: %s instead. %s locator '%s': '%s'.",
                    $this->expectedElementTextFragment,
                    implode(',', $this->results),
                    strtoupper($locator->getType()),
                    $locator->getIdentifier(),
                    $locator->getSelector()
                )
                :
                sprintf(
                    "Could not find element with text containing: '%s'. Collection is empty. %s locator '%s': '%s'.",
                    $this->expectedElementTextFragment,
                    strtoupper($locator->getType()),
                    $locator->getIdentifier(),
                    $locator->getSelector()
                );
    }
}
