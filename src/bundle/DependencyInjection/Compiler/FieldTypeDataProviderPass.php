<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\DependencyInjection\Compiler;

use EzSystems\BehatBundle\API\ContentData\ContentDataProvider;
use EzSystems\BehatBundle\API\ContentData\FieldTypeData\FieldTypeDataProviderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FieldTypeDataProviderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $contentDataDefinition = $container->findDefinition(ContentDataProvider::class);
        $strategyServiceIds = array_keys($container->findTaggedServiceIds(FieldTypeDataProviderInterface::SERVICE_TAG));

        foreach ($strategyServiceIds as $strategyServiceId) {
            $contentDataDefinition->addMethodCall(
                'addFieldTypeDataProvider',
                array(new Reference($strategyServiceId))
            );
        }
    }
}