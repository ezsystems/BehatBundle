<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\Context\Api\LimitationParser;

use eZ\Publish\API\Repository\Values\User\Limitation\ParentUserGroupLimitation;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Values\User\Limitation;

class ParentUserGroupLimitationParser implements LimitationParserInterface
{
    public function supports(string $limitationType): bool
    {
        return $limitationType === Limitation::PARENTUSERGROUP ||
            strtolower($limitationType) === 'parent group';
    }

    public function parse(string $limitationValues): Limitation
    {
        if ($limitationValues !== 'self') {
            throw new InvalidArgumentException('limitationValues', 'only "self" is supported"');
        }

        return new ParentUserGroupLimitation(
            ['limitationValues' => [1]]
        );
    }
}
