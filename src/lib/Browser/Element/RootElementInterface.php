<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element;

interface RootElementInterface extends BaseElementInterface
{
    public function dragAndDrop(string $from, string $hover, string $to): void;
}
