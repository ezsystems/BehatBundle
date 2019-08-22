<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\Context\Api\LimitationParser;

use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\API\Repository\Values\User\Limitation\SiteAccessLimitation;

class SiteaccessLimitationParser implements LimitationParserInterface
{
    public function supports(string $limitationType): bool
    {
        return strtolower($limitationType) === strtolower(Limitation::SITEACCESS);
    }

    public function parse(string $limitationValues): Limitation
    {
        $values = [];

        // code taken from: https://doc.ezplatform.com/en/latest/guide/limitations/#siteaccesslimitation
        foreach (explode(',', $limitationValues) as $siteAccessName) {
            $values[] = sprintf('%u', crc32($siteAccessName));
        }

        return new SiteAccessLimitation(
            ['limitationValues' => $values]
        );
    }
}
