<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Condition;

use Ibexa\Behat\Browser\Element\BaseElementInterface;
use Ibexa\Behat\Browser\Locator\LocatorInterface;

class ElementTransitionHasEndedCondition implements ConditionInterface
{
    private const TRANSITION_ENDED_CLASS = 'ibexa-selenium-transition-ended';

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
        $hasTransitionEndedClass = $this->searchedNode->find($this->elementLocator)->hasClass(self::TRANSITION_ENDED_CLASS);
        $this->searchedNode->setTimeout($currentTimeout);

        return $hasTransitionEndedClass;
    }

    public function getErrorMessage(BaseElementInterface $invokingElement): string
    {
        return sprintf(
            "Transition has not ended for element with %s locator '%s': '%s'. Timeout value: %d seconds.",
            strtoupper($this->elementLocator->getType()),
            $this->elementLocator->getIdentifier(),
            $this->elementLocator->getSelector(),
            $invokingElement->getTimeout()
        );
    }
}
