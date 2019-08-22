<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\Context\Api\LimitationParser;

use eZ\Publish\API\Repository\Values\User\Limitation;

class ParentDepthLimitationParser implements LimitationParserInterface
{
    public function supports(string $limitationType): bool
    {
        return $limitationType === Limitation::PARENTDEPTH ||
            strtolower($limitationType) === 'parent depth';
    }

    public function parse(string $limitationValues): Limitation
    {
        return new Limitation\ParentDepthLimitation(
            ['limitationValues' => [(int) $limitationValues]]
        );
    }
}
