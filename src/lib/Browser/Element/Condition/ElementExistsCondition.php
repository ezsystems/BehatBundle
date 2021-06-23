<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Condition;

use Ibexa\Behat\Browser\Element\RootElement;
use Ibexa\Behat\Browser\Locator\LocatorInterface;

class ElementExistsCondition implements ConditionInterface
{
    /** @var \Ibexa\Behat\Browser\Locator\LocatorInterface */
    private $searchedElementLocator;

    /** @var \Ibexa\Behat\Browser\Element\RootElement */
    private $htmlPage;

    public function __construct(RootElement $htmlPage, LocatorInterface $searchedElementLocator)
    {
        $this->searchedElementLocator = $searchedElementLocator;
        $this->htmlPage = $htmlPage;
    }

    public function isMet(): bool
    {
        return $this->htmlPage
            ->findAll($this->searchedElementLocator)->any();
    }

    public function getErrorMessage(): string
    {
        return sprintf(
            "Element with %s locator '%s': '%s' was not found.",
            $this->searchedElementLocator->getType(),
            $this->searchedElementLocator->getIdentifier(),
            $this->searchedElementLocator->getSelector(),
        );
    }
}
