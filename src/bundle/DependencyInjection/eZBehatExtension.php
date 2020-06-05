<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class eZBehatExtension extends Extension
{
    private const ENABLE_ENTERPRISE_SERVICES = 'ezplatform.behat.enable_enterprise_services';

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
