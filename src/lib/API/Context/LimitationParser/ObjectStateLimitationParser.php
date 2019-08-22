<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\Context\Api\LimitationParser;

use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\API\Repository\Values\User\Limitation\ObjectStateLimitation;

class ObjectStateLimitationParser extends NewStateLimitationParser implements LimitationParserInterface
{
    public function supports(string $limitationType): bool
    {
        return $limitationType === Limitation::STATE;
    }

    public function parse(string $limitationValues): Limitation
    {
        $givenStateIdentifiers = explode(',', $limitationValues);

        return new ObjectStateLimitation(
            ['limitationValues' => $this->parseObjectStateValues($givenStateIdentifiers)]
        );
    }
}
