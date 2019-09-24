<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\Context\Api\LimitationParser;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\URLAliasService;
use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\API\Repository\Values\User\Limitation\LocationLimitation;
use EzSystems\BehatBundle\Helper\ArgumentParser;

class LocationLimitationParser implements LimitationParserInterface
{
    private $locationService;
    private $urlAliasService;
    private $argumentParser;

    public function __construct(URLAliasService $urlAliasService, LocationService $locationService, ArgumentParser $argumentParser)
    {
        $this->urlAliasService = $urlAliasService;
        $this->locationService = $locationService;
        $this->argumentParser = $argumentParser;
    }

    public function supports(string $limitationType): bool
    {
        return $limitationType === Limitation::LOCATION || strtolower($limitationType) === 'location';
    }

    public function parse(string $limitationValues): Limitation
    {
        $values = [];

        foreach (explode(',', $limitationValues) as $limitationValue) {
            $parsedUrl = $this->argumentParser->parseUrl($limitationValue);
            $urlAlias = $this->urlAliasService->lookup($parsedUrl);
            $location = $this->locationService->loadLocation($urlAlias->destination);
            $values[] = $location->id;
        }

        return new LocationLimitation(
            ['limitationValues' => $values]
        );
    }
}
