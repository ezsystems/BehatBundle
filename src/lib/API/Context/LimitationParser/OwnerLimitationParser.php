<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\Context\LimitationParser;

use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\API\Repository\Values\User\Limitation\OwnerLimitation;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;

class OwnerLimitationParser implements LimitationParserInterface
{
    public function supports(string $limitationType): bool
    {
        return $limitationType === Limitation::OWNER;
    }

    public function parse(string $limitationValues): Limitation
    {
        if ($limitationValues !== 'self') {
            throw new InvalidArgumentException('limitationValues', 'only "self" is supported"');
        }

        return new OwnerLimitation(
            ['limitationValues' => [1]]
        );
    }
}
