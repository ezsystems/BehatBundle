<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\BehatBundle;

use Behat\Behat\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use EzSystems\BehatBundle\Initializer\BehatSiteAccessInitializer;
use FriendsOfBehat\SymfonyExtension\ServiceContainer\SymfonyExtension;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class BehatExtension implements Extension
{
    private const MINK_DEFAULT_JAVASCRIPT_SESSION_PARAMETER = 'ibexa.platform.behat.mink.default_javascript_session';

    public function getConfigKey()
    {
        return 'ezbehatextension';
    }

    public function process(ContainerBuilder $container)
    {
        if ($container->hasParameter(self::MINK_DEFAULT_JAVASCRIPT_SESSION_PARAMETER)) {
            $defaultJavascriptSession = $container->getParameter(self::MINK_DEFAULT_JAVASCRIPT_SESSION_PARAMETER);
            $this->setDefaultJavascriptSession($container, $defaultJavascriptSession);
        }
    }

    public function initialize(ExtensionManager $extensionManager)
    {
    }

    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->arrayNode('mink')
                    ->children()
                        ->scalarNode('default_javascript_session')->defaultNull()->end()
                    ->end()
                ->end()
            ->end();
    }

    public function load(ContainerBuilder $container, array $config)
    {
        $this->loadSiteAccessInitializer($container);
        $this->setMinkParameters($container, $config);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/Resources/config'));
        $loader->load('extension.yaml');
    }

    private function loadSiteAccessInitializer(ContainerBuilder $container): void
    {
        $definition = new Definition(BehatSiteAccessInitializer::class);
        $definition->setArguments([
            new Reference(SymfonyExtension::KERNEL_ID),
        ]);
        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, ['priority' => 0]);
        $container->setDefinition(BehatSiteAccessInitializer::class, $definition);
    }

    private function setMinkParameters(ContainerBuilder $container, array $config): void
    {
        if (!array_key_exists('mink', $config)) {
            return;
        }

        $keyParameterMap = [
            'default_javascript_session' => self::MINK_DEFAULT_JAVASCRIPT_SESSION_PARAMETER,
        ];

        foreach ($keyParameterMap as $key => $parameter) {
            $value = $container->resolveEnvPlaceholders($config['mink'][$key], true);
            if ($value) {
                $container->setParameter($parameter, $value);
            }
        }
    }

    private function setDefaultJavascriptSession(ContainerBuilder $container, string $defaultJavascriptSession): void
    {
        $container->setParameter('mink.javascript_session', $defaultJavascriptSession);
    }
}
