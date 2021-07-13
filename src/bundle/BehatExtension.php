<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\BehatBundle;

use Behat\Behat\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use DMore\ChromeDriver\ChromeDriver;
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
    private const MINK_BASE_URL_PARAMETER = 'ibexa.platform.behat.mink.base_url';

    private const MINK_DEFAULT_JAVASCRIPT_SESSION_PARAMETER = 'ibexa.platform.behat.mink.default_javascript_session';

    private const MINK_SELENIUM_WEBDRIVER_HOST = 'ibexa.platform.behat.mink.selenium.webdriver_host';

    private const MINK_CHROME_API_URL = 'ibexa.platform.behat.mink.chrome.api_url';

    public function getConfigKey()
    {
        return 'ezbehatextension';
    }

    public function process(ContainerBuilder $container)
    {
        if ($container->hasParameter(self::MINK_BASE_URL_PARAMETER)) {
            $baseUrl = $container->getParameter(self::MINK_BASE_URL_PARAMETER);
            $this->setBaseUrl($container, $baseUrl);
        }

        if ($container->hasParameter(self::MINK_DEFAULT_JAVASCRIPT_SESSION_PARAMETER)) {
            $defaultJavascriptSession = $container->getParameter(self::MINK_DEFAULT_JAVASCRIPT_SESSION_PARAMETER);
            $this->setDefaultJavascriptSession($container, $defaultJavascriptSession);
        }

        if ($container->hasParameter(self::MINK_SELENIUM_WEBDRIVER_HOST)) {
            $seleniumWebdriverHost = $container->getParameter(self::MINK_SELENIUM_WEBDRIVER_HOST);
            $this->setSeleniumWebdriverHost($container, $seleniumWebdriverHost);
        }

        if ($container->hasParameter(self::MINK_CHROME_API_URL)) {
            $chromeApiUrl = $container->getParameter(self::MINK_CHROME_API_URL);
            $this->setChromeApiUrl($container, $chromeApiUrl);
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
                        ->scalarNode('base_url')->defaultNull()->end()
                        ->scalarNode('default_javascript_session')->defaultNull()->end()
                        ->scalarNode('selenium_webdriver_host')->defaultNull()->end()
                        ->scalarNode('chrome_api_url')->defaultNull()->end()
                    ->end()
                ->end()
            ->end();
    }

    public function load(ContainerBuilder $container, array $config)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/Resources/config')
        );

        $this->loadSiteAccessInitializer($container);
        $this->setMinkParameters($container, $config);
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
            'base_url' => self::MINK_BASE_URL_PARAMETER,
            'default_javascript_session' => self::MINK_DEFAULT_JAVASCRIPT_SESSION_PARAMETER,
            'selenium_webdriver_host' => self::MINK_SELENIUM_WEBDRIVER_HOST,
            'chrome_api_url' => self::MINK_CHROME_API_URL,
        ];

        foreach ($keyParameterMap as $key => $parameter) {
            $value = $container->resolveEnvPlaceholders($config['mink'][$key], true);
            if ($value) {
                $container->setParameter($parameter, $value);
            }
        }
    }

    private function setBaseUrl(ContainerBuilder $container, string $baseUrl): void
    {
        $container->setParameter('mink.base_url', $baseUrl);
        $parameters = $container->getParameter('mink.parameters');
        $parameters['base_url'] = $baseUrl;
        $container->setParameter('mink.parameters', $parameters);
    }

    private function setDefaultJavascriptSession(ContainerBuilder $container, string $defaultJavascriptSession): void
    {
        $container->setParameter('mink.javascript_session', $defaultJavascriptSession);
    }

    private function setSeleniumWebdriverHost(ContainerBuilder $container, string $seleniumWebdriverHost): void
    {
        foreach ($this->getDriverDefinitions($container) as $driverDefinition) {
            if (is_a($driverDefinition->getClass(), Selenium2Driver::class, true)) {
                $driverDefinition->setArgument(2, $seleniumWebdriverHost);
            }
        }
    }

    private function setChromeApiUrl(ContainerBuilder $container, string $chromeApiUrl): void
    {
        foreach ($this->getDriverDefinitions($container) as $driverDefinition) {
            if (is_a($driverDefinition->getClass(), ChromeDriver::class, true)) {
                $driverDefinition->setArgument(0, $chromeApiUrl);
            }
        }
    }

    /**
     * @return \Symfony\Component\DependencyInjection\Definition[]
     */
    private function getDriverDefinitions(ContainerBuilder $container): array
    {
        $minkDefinition = $container->getDefinition('mink');

        $registerSessionCalls = array_filter($minkDefinition->getMethodCalls(), static function ($methodCall) {
            return $methodCall[0] === 'registerSession';
        });

        return array_map(static function (array $registerSessionCall) {
            /** @var \Symfony\Component\DependencyInjection\Definition $sessionDefinition */
            $sessionDefinition = $registerSessionCall[1][1];

            return $sessionDefinition->getArgument(0);
        }, $registerSessionCalls);
    }
}
