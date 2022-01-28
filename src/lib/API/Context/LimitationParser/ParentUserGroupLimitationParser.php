<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\Context\LimitationParser;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\ParentUserGroupLimitation;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

class ParentUserGroupLimitationParser implements LimitationParserInterface
{
    public function supports(string $limitationType): bool
    {
        return Limitation::PARENTUSERGROUP === $limitationType
            || 'parent group' === strtolower($limitationType);
    }

    public function parse(string $limitationValues): Limitation
    {
        if ('self' !== $limitationValues) {
            throw new InvalidArgumentException('limitationValues', 'only "self" is supported"');
        }

        return new ParentUserGroupLimitation(
            ['limitationValues' => [1]]
        );
    }
}
