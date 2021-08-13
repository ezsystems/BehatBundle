<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Condition;

use Ibexa\Behat\Browser\Element\BaseElementInterface;
use Ibexa\Behat\Browser\Locator\LocatorInterface;

class ElementsCountGreaterThanCondition implements ConditionInterface
{
    /** @var \Ibexa\Behat\Browser\Locator\LocatorInterface */
    private $elementsLocator;

    /** @var \Ibexa\Behat\Browser\Element\BaseElementInterface */
    private $searchedNode;

    /** @var int */
    private $expectedElementsCount;

    /** @var int */
    private $actualNumberOfItemsFound;

    public function __construct(BaseElementInterface $searchedNode, LocatorInterface $elementsLocator, int $expectedElementsCount)
    {
        $this->elementsLocator = $elementsLocator;
        $this->searchedNode = $searchedNode;
        $this->expectedElementsCount = $expectedElementsCount;
    }

    public function isMet(): bool
    {
        $currentTimeout = $this->searchedNode->getTimeout();
        $this->searchedNode->setTimeout(0);
        $actualCount = $this->searchedNode->findAll($this->elementsLocator)->count();
        $this->searchedNode->setTimeout($currentTimeout);
        $this->actualNumberOfItemsFound = $actualCount;

        return $actualCount > $this->expectedElementsCount;
    }

    public function getErrorMessage(BaseElementInterface $invokingElement): string
    {
        return sprintf(
            "The found number of items (%d) matching %s locator '%s': '%s' was not greater than expected value (%d). Timeout value: %d seconds.",
            $this->actualNumberOfItemsFound,
            strtoupper($this->elementsLocator->getType()),
            $this->elementsLocator->getIdentifier(),
            $this->elementsLocator->getSelector(),
            $this->expectedElementsCount,
            $invokingElement->getTimeout()
        );
    }
}
