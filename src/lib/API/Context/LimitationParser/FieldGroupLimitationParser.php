<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\API\Context\LimitationParser;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Contracts\Permissions\Repository\Values\User\Limitation\FieldGroupLimitation;

class FieldGroupLimitationParser implements LimitationParserInterface
{
    public function supports(string $limitationType): bool
    {
        return \in_array(strtolower($limitationType), ['fieldgroup', 'field group']);
    }

    public function parse(string $limitationValues): Limitation
    {
        $limitations = explode(',', $limitationValues);

        return new FieldGroupLimitation(
            ['limitationValues' => $limitations]
        );
    }
}

class_alias(FieldGroupLimitationParser::class, 'EzSystems\Behat\API\Context\LimitationParser\FieldGroupLimitationParser');
