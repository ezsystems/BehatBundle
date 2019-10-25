<?php


namespace EzSystems\BehatBundle\DependencyInjection\Compiler;

use EzSystems\Behat\Test\Factory\PageObjectFactory;
use EzSystems\Behat\Test\PageObject\PageInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PageObjectPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $pageFactory = $container->findDefinition(PageObjectFactory::class);

        $strategyServiceIds = array_keys($container->findTaggedServiceIds(PageInterface::PAGE_OBJECT_TAG));

        foreach ($strategyServiceIds as $strategyServiceId) {
            $pageFactory->addMethodCall(
                'add',
                [new Reference($strategyServiceId)]
            );
        }
    }
}