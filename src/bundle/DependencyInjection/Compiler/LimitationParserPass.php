<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\DependencyInjection\Compiler;


use EzSystems\BehatBundle\API\Context\LimitationParser\LimitationParserInterface;
use EzSystems\BehatBundle\API\Context\LimitationParser\LimitationParsersCollector;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class LimitationParserPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $parserCollector = $container->findDefinition(LimitationParsersCollector::class);
        $strategyServiceIds = array_keys($container->findTaggedServiceIds(LimitationParserInterface::SERVICE_TAG));

        foreach ($strategyServiceIds as $strategyServiceId) {
            $parserCollector->addMethodCall(
                'addLimitationParser',
                array(new Reference($strategyServiceId))
            );
        }
    }
}