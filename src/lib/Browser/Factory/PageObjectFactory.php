<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\Browser\Factory;

use EzSystems\Behat\Browser\Context\BrowserContext;
use EzSystems\Behat\Browser\Page\FrontendLoginPage;
use EzSystems\Behat\Core\Environment\InstallType;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\PlatformPageObjectFactory;
use EzSystems\EzPlatformPageBuilder\Tests\Behat\PageObject\EnterprisePageObjectFactory;

class PageObjectFactory
{
    private static $installType;

    private static $factory;

    /**
     * Creates a Page object based on given Page Name.
     *
     * @param UtilityContext $context
     * @param string $pageName Name of the Page to creator
     * @param null[]|string[] $parameters additional parameters
     */
    public static function createPage(BrowserContext $context, string $pageName, ?string ...$parameters)
    {
        /* Note: no return type to enable type-hinting */

        if (self::$factory === null) {
            self::$factory = self::getFactory(self::$installType);
        }

        switch ($pageName) {
            case FrontendLoginPage::PAGE_NAME:
                return new FrontendLoginPage($context);
        }

        return self::$factory::createPage($context, $pageName, ...$parameters);
    }

    public static function setInstallType(int $installType)
    {
        self::$installType = $installType;
    }

    public static function getPreviewType(string $contentType)
    {
        /* Note: no return type to enable type-hinting */
        $factory = self::getFactory(self::$installType);

        return $factory::getPreviewType($contentType);
    }

    /**
     * @param int $installType
     *
     * @throws \Exception
     */
    private static function getFactory(int $installType)
    {
        switch ($installType) {
            case InstallType::OSS:
            case InstallType::CONTENT:
                return new PlatformPageObjectFactory();
            case InstallType::EXPERIENCE:
            case InstallType::COMMERCE:
                return new EnterprisePageObjectFactory();
            default:
                throw new \Exception('Unrecognised install type');
        }
    }
}
