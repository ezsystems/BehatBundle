<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Condition;

use Ibexa\Behat\Browser\Element\BaseElementInterface;

interface ConditionInterface
{
    public function isMet(): bool;

    public function getErrorMessage(BaseElementInterface $invokingElement): string;
}
