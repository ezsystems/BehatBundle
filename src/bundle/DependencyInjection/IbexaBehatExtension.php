<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Behat\DependencyInjection;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Page\PageInterface;
use Ibexa\Behat\Browser\Page\Preview\PagePreviewInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Ibexa\Behat\API\ContentData\FieldTypeData\FieldTypeDataProviderInterface;
use Ibexa\Behat\API\Context\LimitationParser\LimitationParserInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class IbexaBehatExtension extends Extension implements PrependExtensionInterface, CompilerPassInterface
{
    private const OVERRIDE_CONFIGURATION = 'ibexa.testing.override_configuration';

    private const BROWSER_TESTING_ENABLED = 'ibexa.testing.browser.enabled';

    public const BROWSER_DEBUG_INTERACTIVE_ENABLED = 'ibexa.testing.behat.browser.debug.interactive.enabled';

    public function process(ContainerBuilder $container)
    {
        if (!$container->getParameter(self::OVERRIDE_CONFIGURATION)) {
            return;
        }

        $container->setParameter('ibexa.site_access.config.admin_group.notifications.success.timeout', 20000);
    }

    public function prepend(ContainerBuilder $container)
    {
        $container->setParameter(self::OVERRIDE_CONFIGURATION, true);
        $container->setParameter(self::BROWSER_TESTING_ENABLED, true);
        $container->setParameter(self::BROWSER_DEBUG_INTERACTIVE_ENABLED, false);
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

        $container->registerForAutoconfiguration(Component::class)
            ->addTag('ibexa.testing.browser.component');

        $container->registerForAutoconfiguration(PageInterface::class)
            ->addTag('ibexa.testing.browser.page');

        $container->registerForAutoconfiguration(PagePreviewInterface::class)
            ->addTag('ibexa.testing.browser.page_preview');

        $container->registerForAutoconfiguration(FieldTypeDataProviderInterface::class)
            ->addTag('ezplatform.behat.fieldtype_data_provider');

        $container->registerForAutoconfiguration(LimitationParserInterface::class)
            ->addTag('ezplatform.behat.limitation_parser');
    }

    private function shouldLoadDxpServices(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        return isset($bundles['IbexaPageBuilderBundle'], $bundles['IbexaWorkflowBundle']);
    }
}

class_alias(IbexaBehatExtension::class, 'EzSystems\BehatBundle\DependencyInjection\eZBehatExtension');
