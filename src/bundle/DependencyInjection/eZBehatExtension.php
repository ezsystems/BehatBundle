<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\BehatBundle\DependencyInjection;

use Ibexa\Behat\Browser\Page\PageInterface;
use Ibexa\Behat\Browser\Page\Preview\PagePreviewInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class eZBehatExtension extends Extension implements PrependExtensionInterface, CompilerPassInterface
{
    private const OVERRIDE_CONFIGURATION = 'ibexa.testing.override_configuration';

    private const BROWSER_TESTING_ENABLED = 'ibexa.testing.browser.enabled';

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
        $container->setParameter(self::BROWSER_TESTING_ENABLED, true);
    }

    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yaml');

        if ($this->shouldLoadDxpServices($container)) {
            $loader->load('services_dxp.yaml');
        }

        $container->registerForAutoconfiguration(PageInterface::class)
            ->addTag('ibexa.testing.browser.page')
        ;

        $container->registerForAutoconfiguration(PagePreviewInterface::class)
            ->addTag('ibexa.testing.browser.page_preview')
        ;
    }

    private function shouldLoadDxpServices(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        return isset($bundles['EzPlatformPageBuilderBundle'], $bundles['EzPlatformWorkflowBundle']);
    }
}
