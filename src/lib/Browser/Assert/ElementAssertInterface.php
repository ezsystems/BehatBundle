<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Assert;

use Ibexa\Behat\Browser\Element\ElementInterface;

interface ElementAssertInterface
{
    public function textEquals(string $expectedText): ElementInterface;

    public function textContains(string $expectedTextFragment): ElementInterface;

    public function isVisible(): ElementInterface;
}
