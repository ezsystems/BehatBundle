<?php
/**
 * File containing the Enviroment Cleaner helper for BehatBundle
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Helper;

use EzSystems\PlatformInstallerBundle\Installer\DbBasedInstaller as Installer;
use Symfony\Component\Console\Output\NullOutput;

/**
 * EzBehat Database Cleaner class
 * Makes sure the enviroment is cleaned only once
 */
class EnvironmentCleaner
{
    /**
     * @var bool stores the state of the enviroment if it is true it needs
     */
    private static $isDirty = true;

    /**
     * @param Installer $installer EzPlatform Installer, contains the setup methods for the enviroment
     */
    public static function cleanEnvironment(Installer $installer)
    {
        if (static::$isDirty) {
            $installer->setOutput(new NullOutput());
            $installer->importSchema();
            $installer->importData();
            $installer->importBinaries();
            static::$isDirty = false;
        }
    }
}
