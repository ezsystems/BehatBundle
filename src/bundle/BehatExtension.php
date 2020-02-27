<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\BehatBundle;

use Behat\Behat\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Symfony2Extension\ServiceContainer\Symfony2Extension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use EzSystems\BehatBundle\Initializer\BehatSiteAccessInitializer;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class BehatExtension implements Extension
{
    public function getConfigKey()
    {
        return 'ezbehatextension';
    }

    public function process(ContainerBuilder $container)
    {
    }

    public function initialize(ExtensionManager $extensionManager)
    {
    }

    public function configure(ArrayNodeDefinition $builder)
    {
    }

    public function load(ContainerBuilder $container, array $config)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/Resources/config')
        );
        $loader->load('extension.yaml');

        $this->loadSiteAccessInitializer($container);
    }

    private function loadSiteAccessInitializer(ContainerBuilder $container): void
    {
        $definition = new Definition(BehatSiteAccessInitializer::class);
        $definition->setArguments([
            new Reference(Symfony2Extension::KERNEL_ID),
        ]);
        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, ['priority' => 0]);
        $container->setDefinition(BehatSiteAccessInitializer::class, $definition);
    }
}
