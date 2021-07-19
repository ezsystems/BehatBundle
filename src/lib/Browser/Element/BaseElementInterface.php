<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element;

use Ibexa\Behat\Browser\Element\Condition\ConditionInterface;
use Ibexa\Behat\Browser\Locator\LocatorInterface;

interface BaseElementInterface
{
    public function setTimeout(int $timeoutSeconds): BaseElementInterface;

    public function getTimeout(): int;

    public function find(LocatorInterface $locator): ElementInterface;

    public function findAll(LocatorInterface $locator): ElementCollection;

    public function waitUntilCondition(ConditionInterface $condition): BaseElementInterface;

    public function waitUntil(callable $callback, string $errorMessage);
}
