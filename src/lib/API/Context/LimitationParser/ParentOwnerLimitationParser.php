<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\Context\LimitationParser;

use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\API\Repository\Values\User\Limitation\ParentOwnerLimitation;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;

class ParentOwnerLimitationParser implements LimitationParserInterface
{
    public function supports(string $limitationType): bool
    {
        return $limitationType === Limitation::PARENTOWNER ||
            strtolower($limitationType) === 'parent owner';
    }

    public function parse(string $limitationValues): Limitation
    {
        if ($limitationValues !== 'self') {
            throw new InvalidArgumentException('limitationValues', 'only "self" is supported"');
        }

        return new ParentOwnerLimitation(
            ['limitationValues' => [1]]
        );
    }
}
