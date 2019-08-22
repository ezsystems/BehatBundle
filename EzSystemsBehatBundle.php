<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle;

use EzSystems\BehatBundle\DependencyInjection\Compiler\FieldTypeDataProviderPass;
use EzSystems\BehatBundle\DependencyInjection\Compiler\LimitationParserPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EzSystemsBehatBundle extends Bundle
{
    protected $name = 'eZBehatBundle';

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FieldTypeDataProviderPass());
        $container->addCompilerPass(new LimitationParserPass());
    }
}
