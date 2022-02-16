<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Behat;

use Ibexa\Bundle\Behat\DependencyInjection\Compiler\ElementFactoryCompilerPass;
use Ibexa\Bundle\Behat\DependencyInjection\Compiler\FieldTypeDataProviderPass;
use Ibexa\Bundle\Behat\DependencyInjection\Compiler\LimitationParserPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IbexaBehatBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FieldTypeDataProviderPass());
        $container->addCompilerPass(new LimitationParserPass());
        $container->addCompilerPass(new ElementFactoryCompilerPass());
    }
}

class_alias(IbexaBehatBundle::class, 'EzSystems\BehatBundle\EzSystemsBehatBundle');
