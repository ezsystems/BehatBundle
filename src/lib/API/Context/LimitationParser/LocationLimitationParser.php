<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\API\Context\LimitationParser;

use Ibexa\Behat\Core\Behat\ArgumentParser;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\URLAliasService;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\LocationLimitation;

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
        return Limitation::LOCATION === $limitationType || 'location' === strtolower($limitationType);
    }

    public function parse(string $limitationValues): Limitation
    {
        $values = [];

        foreach (explode(',', $limitationValues) as $limitationValue) {
            $parsedUrl = $this->argumentParser->parseUrl($limitationValue);
            try {
                $urlAlias = $this->urlAliasService->lookup($parsedUrl);
                $location = $this->locationService->loadLocation($urlAlias->destination);
            } catch (NotFoundException $exception) {
                continue;
            }

            $values[] = $location->id;
        }

        return new LocationLimitation(
            ['limitationValues' => $values]
        );
    }
}

class_alias(LocationLimitationParser::class, 'EzSystems\Behat\API\Context\LimitationParser\LocationLimitationParser');
