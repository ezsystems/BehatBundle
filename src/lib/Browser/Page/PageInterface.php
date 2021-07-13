<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Page;

use Ibexa\Behat\Browser\Component\ComponentInterface;

interface PageInterface extends ComponentInterface
{
    public function open(string $siteaccess): void;

    public function tryToOpen(string $siteaccess): void;

    public function getName(): string;
}
