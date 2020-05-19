<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;

class eZBehatExtension extends Extension implements PrependExtensionInterface
{
    private const CONFIG_PREFIX = 'ez_platform_behat';

    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yml');

        if ($container->getParameter('ezplatform_behat.is_enterprise')) {
            $loader->load('services_enterprise.yaml');
        }
    }

    public function prepend(ContainerBuilder $container)
    {
        $config = Yaml::parseFile(
            __DIR__ . '/../Resources/config/settings.yaml'
        );

        $container->prependExtensionConfig(self::CONFIG_PREFIX, $config);
    }
}
