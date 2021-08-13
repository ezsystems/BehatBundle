<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\BehatBundle;

use EzSystems\BehatBundle\DependencyInjection\Compiler\ElementFactoryCompilerPass;
use EzSystems\BehatBundle\DependencyInjection\Compiler\FieldTypeDataProviderPass;
use EzSystems\BehatBundle\DependencyInjection\Compiler\LimitationParserPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzSystemsBehatBundle extends Bundle
{
    protected $name = 'eZBehatBundle';

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FieldTypeDataProviderPass());
        $container->addCompilerPass(new LimitationParserPass());
        $container->addCompilerPass(new ElementFactoryCompilerPass());
    }
}
