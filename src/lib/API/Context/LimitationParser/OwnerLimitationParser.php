<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\API\Context\LimitationParser;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\OwnerLimitation;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

class OwnerLimitationParser implements LimitationParserInterface
{
    public function supports(string $limitationType): bool
    {
        return Limitation::OWNER === $limitationType;
    }

    public function parse(string $limitationValues): Limitation
    {
        if ('self' !== $limitationValues) {
            throw new InvalidArgumentException('limitationValues', 'only "self" is supported"');
        }

        return new OwnerLimitation(
            ['limitationValues' => [1]]
        );
    }
}

class_alias(OwnerLimitationParser::class, 'EzSystems\Behat\API\Context\LimitationParser\OwnerLimitationParser');
