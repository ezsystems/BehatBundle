<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Behat\DependencyInjection\Compiler;

use Ibexa\Behat\API\ContentData\ContentDataProvider;
use Ibexa\Behat\API\ContentData\FieldTypeData\FieldTypeDataProviderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FieldTypeDataProviderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $contentDataDefinition = $container->findDefinition(ContentDataProvider::class);
        $strategyServiceIds = array_keys($container->findTaggedServiceIds('ezplatform.behat.fieldtype_data_provider'));

        foreach ($strategyServiceIds as $strategyServiceId) {
            $contentDataDefinition->addMethodCall(
                'addFieldTypeDataProvider',
                [new Reference($strategyServiceId)]
            );
        }
    }
}

class_alias(FieldTypeDataProviderPass::class, 'EzSystems\BehatBundle\DependencyInjection\Compiler\FieldTypeDataProviderPass');
