<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\Context\LimitationParser;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\ObjectStateLimitation;

class ObjectStateLimitationParser extends NewStateLimitationParser implements LimitationParserInterface
{
    public function supports(string $limitationType): bool
    {
        return Limitation::STATE === $limitationType;
    }

    public function parse(string $limitationValues): Limitation
    {
        $givenStateIdentifiers = explode(',', $limitationValues);

        return new ObjectStateLimitation(
            ['limitationValues' => $this->parseObjectStateValues($givenStateIdentifiers)]
        );
    }
}
