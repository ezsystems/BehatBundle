<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\Core\Environment;

abstract class InstallType
{
    public const PLATFORM = 1;
    public const PLATFORM_DEMO = 2;
    public const ENTERPRISE = 3;
    public const ENTERPRISE_DEMO = 4;
    public const COMMERCE = 5;

    public const PACKAGE_NAME_MAP = [
        'ezsystems/ezplatform' => InstallType::PLATFORM,
        'ezsystems/ezplatform-ee' => InstallType::ENTERPRISE,
        'ezsystems/ezplatform-demo' => InstallType::PLATFORM_DEMO,
        'ezsystems/ezplatform-ee-demo' => InstallType::ENTERPRISE_DEMO,
        'ezsystems/ezcommerce' => InstallType::COMMERCE,
    ];
}
