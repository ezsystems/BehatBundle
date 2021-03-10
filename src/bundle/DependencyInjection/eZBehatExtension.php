<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Loader;
use EzSystems\Behat\Core\Environment\Environment;
use EzSystems\Behat\Core\Environment\InstallType;

class eZBehatExtension extends Extension implements PrependExtensionInterface, CompilerPassInterface
{
    private const ENABLE_ENTERPRISE_SERVICES = 'ezplatform.behat.enable_enterprise_services';

    private const OVERRIDE_CONFIGURATION = 'ezplatform.behat.override_configuration';

    public function process(ContainerBuilder $container)
    {
        if (!$container->getParameter(self::OVERRIDE_CONFIGURATION)) {
            return;
        }

        $container->setParameter('ezsettings.admin_group.notifications.success.timeout', 20000);
    }

    public function prepend(ContainerBuilder $container)
    {
        $container->setParameter(self::OVERRIDE_CONFIGURATION, true);

        $env = new Environment($container);
        $installType = $env->getInstallType();

        if (\in_array($installType, [InstallType::CONTENT, InstallType::EXPERIENCE, InstallType::COMMERCE])) {
            $container->setParameter(self::ENABLE_ENTERPRISE_SERVICES, true);
        }
    }

    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yaml');

        if ($this->shouldLoadEnterpriseServices($container)) {
            $loader->load('services_enterprise.yaml');
        }
    }

    private function shouldLoadEnterpriseServices(ContainerBuilder $container): bool
    {
        if (!$container->hasParameter(self::ENABLE_ENTERPRISE_SERVICES)) {
            return false;
        }

        return (bool)$container->getParameter(self::ENABLE_ENTERPRISE_SERVICES);
    }
}
