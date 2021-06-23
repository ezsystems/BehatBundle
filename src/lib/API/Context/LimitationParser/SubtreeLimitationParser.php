<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\Context\LimitationParser;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\URLAliasService;
use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\API\Repository\Values\User\Limitation\SubtreeLimitation;

class SubtreeLimitationParser implements LimitationParserInterface
{
    private $locationService;
    private $urlAliasService;

    public function __construct(URLAliasService $urlAliasService, LocationService $locationService)
    {
        $this->urlAliasService = $urlAliasService;
        $this->locationService = $locationService;
    }

    public function supports(string $limitationType): bool
    {
        return Limitation::SUBTREE === $limitationType;
    }

    public function parse(string $limitationValues): Limitation
    {
        $values = [];

        foreach (explode(',', $limitationValues) as $limitationValue) {
            $urlAlias = $this->urlAliasService->lookup($limitationValue);
            $location = $this->locationService->loadLocation($urlAlias->destination);
            $values[] = $location->pathString;
        }

        return new SubtreeLimitation(
            ['limitationValues' => $values]
        );
    }
}
