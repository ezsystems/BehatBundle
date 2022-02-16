<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\API\Context\LimitationParser;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;

interface LimitationParserInterface
{
    public function supports(string $limitationType): bool;

    public function parse(string $limitationValues): Limitation;
}

class_alias(LimitationParserInterface::class, 'EzSystems\Behat\API\Context\LimitationParser\LimitationParserInterface');
