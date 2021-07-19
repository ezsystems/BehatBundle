<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Condition;

use Ibexa\Behat\Browser\Element\BaseElementInterface;
use Ibexa\Behat\Browser\Locator\LocatorInterface;

class ElementExistsCondition implements ConditionInterface
{
    /** @var \Ibexa\Behat\Browser\Locator\LocatorInterface */
    private $elementLocator;

    /** @var \Ibexa\Behat\Browser\Element\BaseElementInterface */
    private $searchedNode;

    public function __construct(BaseElementInterface $searchedNode, LocatorInterface $elementLocator)
    {
        $this->elementLocator = $elementLocator;
        $this->searchedNode = $searchedNode;
    }

    public function isMet(): bool
    {
        $currentTimeout = $this->searchedNode->getTimeout();
        $this->searchedNode->setTimeout(0);
        $elementExists = $this->searchedNode->findAll($this->elementLocator)->any();
        $this->searchedNode->setTimeout($currentTimeout);

        return $elementExists;
    }

    public function getErrorMessage(BaseElementInterface $invokingElement): string
    {
        return sprintf(
            "Element with %s locator '%s': '%s' was not found. Timeout value: %d seconds.",
            strtoupper($this->elementLocator->getType()),
            $this->elementLocator->getIdentifier(),
            $this->elementLocator->getSelector(),
            $invokingElement->getTimeout()
        );
    }
}
