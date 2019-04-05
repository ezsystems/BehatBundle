<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\Context\LimitationParser;

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

    public function canWork(string $limitationType): bool
    {
        return $limitationType === Limitation::SUBTREE;
    }

    public function parse(string $limitationValue)
    {
        $urlAlias = $this->urlAliasService->lookup($limitationValue);
        $location = $this->locationService->loadLocation($urlAlias->destination);

        return new SubtreeLimitation(
            ['limitationValues' => [$location->pathString]]
        );
    }
}