<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Environment;

interface ParameterProviderInterface
{
    public function getParameter(string $parameterName): string;

    public function setParameter(string $key, $value): void;
}
