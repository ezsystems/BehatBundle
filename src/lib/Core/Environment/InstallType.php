<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\Core\Environment;

abstract class InstallType
{
    public const OSS = 1;
    public const CONTENT = 2;
    public const EXPERIENCE = 3;
    public const COMMERCE = 4;
}
