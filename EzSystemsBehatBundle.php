<?php
/**
 * File containing the EzSystemsBehatBundle class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle;

use EzSystems\BehatBundle\DependencyInjection\Compiler\FieldTypeDataProviderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzSystemsBehatBundle extends Bundle
{
    protected $name = 'eZBehatBundle';

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FieldTypeDataProviderPass());
    }
}
