<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Assert;

use Ibexa\Behat\Browser\Element\ElementCollectionInterface;

interface CollectionAssertInterface
{
    public function isEmpty(): ElementCollectionInterface;

    public function hasElements(): ElementCollectionInterface;

    public function countEquals(int $expectedCount): ElementCollectionInterface;
}
