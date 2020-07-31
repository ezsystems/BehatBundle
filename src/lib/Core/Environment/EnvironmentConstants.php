<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\Core\Environment;

use EzSystems\EzPlatformAdminUi\Behat\Environment\PlatformEnvironmentConstants;
use EzSystems\EzPlatformPageBuilder\Tests\Behat\Environment\EnterpriseEnvironmentConstants;
use Tests\AppBundle\Behat\PlatformDemoEnvironmentConstants;
use Tests\App\Behat\EnterpriseDemoEnvironmentConstants;

class EnvironmentConstants
{
    private static $installType;

    public static function setInstallType(int $installType)
    {
        self::$installType = $installType;
    }

    public static function get(string $key): string
    {
        $env = self::getProperEnvironment(self::$installType);

        return $env->values[$key];
    }

    public static function getInstallType(): string
    {
        return self::$installType;
    }

    public static function isEnterprise(): bool
    {
        return in_array(self::getInstallType(), [InstallType::ENTERPRISE, InstallType::ENTERPRISE_DEMO, InstallType::COMMERCE]);
    }

    private static function getProperEnvironment(int $installType)
    {
        switch ($installType) {
            case InstallType::PLATFORM:
                return new PlatformEnvironmentConstants();
            case InstallType::PLATFORM_DEMO:
                return new PlatformDemoEnvironmentConstants();
            case InstallType::ENTERPRISE:
                return new EnterpriseEnvironmentConstants();
            case InstallType::ENTERPRISE_DEMO:
                return new EnterpriseDemoEnvironmentConstants();
            case InstallType::COMMERCE:
                return new EnterpriseEnvironmentConstants();
            default:
                throw new \Exception('Unrecognised install type');
        }
    }
}
