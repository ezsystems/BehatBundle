<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\Context\LimitationParser;

use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\API\Repository\Values\User\Limitation\OwnerLimitation;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;

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
