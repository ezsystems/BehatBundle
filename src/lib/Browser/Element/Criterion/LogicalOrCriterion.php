<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Criterion;

use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Locator\LocatorInterface;

class LogicalOrCriterion implements CriterionInterface
{
    /** @var \Ibexa\Behat\Browser\Element\Criterion\CriterionInterface[] */
    private $criterions;

    /** @var string[] */
    private $results;

    public function __construct(array $criterions = [])
    {
        $this->criterions = $criterions;
    }

    public function addCriterion(CriterionInterface $criterion): void
    {
        $this->criterions[] = $criterion;
    }

    public function matches(ElementInterface $element): bool
    {
        foreach ($this->criterions as $criterion) {
            if ($criterion->matches($element)) {
                return true;
            }
        }

        return false;
    }

    public function getErrorMessage(LocatorInterface $locator): string
    {
        $errorMessage = 'LogicalOr criterion failed. Condition error messages:' . PHP_EOL;

        foreach ($this->criterions as $criterion) {
            $errorMessage .= $criterion->getErrorMessage($locator) . PHP_EOL;
        }

        return $errorMessage;
    }
}
