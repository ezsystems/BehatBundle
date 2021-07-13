<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Environment;

class ParameterProvider implements ParameterProviderInterface
{
    /** @var array */
    private $parameters;

    public function __construct()
    {
        $this->parameters = [];
        $this->setParameter('root_content_name', 'Ibexa Platform');
    }

    public function setParameter(string $key, $value): void
    {
        $this->parameters[$key] = $value;
    }

    public function getParameter(string $parameterName): string
    {
        return $this->parameters[$parameterName];
    }
}
