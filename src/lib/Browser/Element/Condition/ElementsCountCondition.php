<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Condition;

use Ibexa\Behat\Browser\Element\RootElement;
use Ibexa\Behat\Browser\Locator\LocatorInterface;

class ElementsCountCondition implements ConditionInterface
{
    /** @var \Ibexa\Behat\Browser\Locator\LocatorInterface */
    private $searchedElementLocator;

    /** @var \Ibexa\Behat\Browser\Element\RootElement */
    private $htmlPage;

    /** @var int */
    private $expectedElementsCount;
    /** @var int */
    private $actualNumberOfItemsFound;

    public function __construct(RootElement $htmlPage, LocatorInterface $searchedElementLocator, int $expectedElementsCount)
    {
        $this->searchedElementLocator = $searchedElementLocator;
        $this->htmlPage = $htmlPage;
        $this->expectedElementsCount = $expectedElementsCount;
    }

    public function isMet(): bool
    {
        $actualCount = $this->htmlPage
            ->findAll($this->searchedElementLocator)->count();

        $this->actualNumberOfItemsFound = $actualCount;

        return $actualCount === $this->expectedElementsCount;
    }

    public function getErrorMessage(): string
    {
        return sprintf(
            "The expected number of items (%d) matching %s locator '%s': '%s' was not found. Found %d items instead",
            $this->expectedElementsCount,
            $this->searchedElementLocator->getType(),
            $this->searchedElementLocator->getIdentifier(),
            $this->searchedElementLocator->getSelector(),
            $this->actualNumberOfItemsFound
        );
    }
}
