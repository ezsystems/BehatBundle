<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\Core\Environment;

use EzSystems\EzPlatformAdminUi\Behat\Environment\PlatformEnvironmentConstants;
use EzSystems\EzPlatformPageBuilder\Tests\Behat\Environment\EnterpriseEnvironmentConstants;

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
        return in_array(self::getInstallType(), [InstallType::CONTENT, InstallType::EXPERIENCE, InstallType::COMMERCE]);
    }

    private static function getProperEnvironment(int $installType)
    {
        switch ($installType) {
            case InstallType::OSS:
            case InstallType::CONTENT:
                return new PlatformEnvironmentConstants();
            case InstallType::EXPERIENCE:
            case InstallType::COMMERCE:
                return new EnterpriseEnvironmentConstants();
            default:
                throw new \Exception('Unrecognised install type');
        }
    }
}
