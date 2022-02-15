<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\API\Context\LimitationParser;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\URLAliasService;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\SubtreeLimitation;

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

class_alias(SubtreeLimitationParser::class, 'EzSystems\Behat\API\Context\LimitationParser\SubtreeLimitationParser');
