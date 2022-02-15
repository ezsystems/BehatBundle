<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Behat\Controller;

use Ibexa\Core\Base\Exceptions\UnauthorizedException;

class ExceptionController
{
    public function throwRepositoryUnauthorizedAction($module = 'foo', $function = 'bar', $properties = [])
    {
        throw new UnauthorizedException($module, $function, $properties);
    }
}

class_alias(ExceptionController::class, 'EzSystems\BehatBundle\Controller\ExceptionController');
