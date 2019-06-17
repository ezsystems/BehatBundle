<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\Context\LimitationParser;

use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\API\Repository\Values\User\Limitation\ParentContentTypeLimitation;

class ParentContentTypeLimitationParser extends ContentTypeLimitationParser
{
    public function supports(string $limitationType): bool
    {
        return $limitationType === Limitation::PARENTCONTENTTYPE
            || strtolower($limitationType) === 'parent content type';
    }

    public function parse(string $limitationValues): Limitation
    {
        return new ParentContentTypeLimitation(
            ['limitationValues' => $this->parseContentTypeValues(explode(',', $limitationValues))]
        );
    }
}
