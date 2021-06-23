<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Locator;

class CSSLocator extends BaseLocator
{
    public function getType(): string
    {
        return 'css';
    }

    public static function empty(): CSSLocator
    {
        return new CSSLocator('empty', 'html');
    }
}
