<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\Context\LimitationParser;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;

class ParentDepthLimitationParser implements LimitationParserInterface
{
    public function supports(string $limitationType): bool
    {
        return Limitation::PARENTDEPTH === $limitationType
            || 'parent depth' === strtolower($limitationType);
    }

    public function parse(string $limitationValues): Limitation
    {
        return new Limitation\ParentDepthLimitation(
            ['limitationValues' => [(int) $limitationValues]]
        );
    }
}
