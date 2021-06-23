<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\Context\LimitationParser;

use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\API\Repository\Values\User\Limitation\ParentContentTypeLimitation;

class ParentContentTypeLimitationParser extends ContentTypeLimitationParser
{
    public function supports(string $limitationType): bool
    {
        return Limitation::PARENTCONTENTTYPE === $limitationType
            || 'parent content type' === strtolower($limitationType);
    }

    public function parse(string $limitationValues): Limitation
    {
        return new ParentContentTypeLimitation(
            ['limitationValues' => $this->parseContentTypeValues(explode(',', $limitationValues))]
        );
    }
}
