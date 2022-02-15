<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\API\Context\LimitationParser;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\ParentOwnerLimitation;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

class ParentOwnerLimitationParser implements LimitationParserInterface
{
    public function supports(string $limitationType): bool
    {
        return Limitation::PARENTOWNER === $limitationType
            || 'parent owner' === strtolower($limitationType);
    }

    public function parse(string $limitationValues): Limitation
    {
        if ('self' !== $limitationValues) {
            throw new InvalidArgumentException('limitationValues', 'only "self" is supported"');
        }

        return new ParentOwnerLimitation(
            ['limitationValues' => [1]]
        );
    }
}

class_alias(ParentOwnerLimitationParser::class, 'EzSystems\Behat\API\Context\LimitationParser\ParentOwnerLimitationParser');
