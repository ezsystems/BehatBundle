<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Behat\Templating\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PHPTypeExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('php_type', static function ($var) {
                $type = gettype($var);

                if ($type === 'object') {
                    return get_class($var);
                }

                return $type;
            }),
        ];
    }
}

class_alias(PHPTypeExtension::class, 'EzSystems\BehatBundle\Templating\Twig\PHPTypeExtension');
