<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\BehatBundle\API\Facade;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\URLAliasService;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LocationId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalNot;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Subtree;
use eZ\Publish\API\Repository\Values\Content\URLAlias;
use PHPUnit\Framework\Assert;

class SearchFacade
{
    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\URLAliasService */
    private $urlAliasService;

    /** @var \eZ\Publish\API\Repository\SearchService */
    private $searchService;

    public function __construct(URLAliasService $urlAliasService, LocationService $locationService, SearchService $searchService)
    {
        $this->urlAliasService = $urlAliasService;
        $this->locationService = $locationService;
        $this->searchService = $searchService;
    }

    public function getRandomChildFromPath(string $path): string
    {
        $urlAlias = $this->urlAliasService->lookup($path);
        Assert::assertEquals(URLAlias::LOCATION, $urlAlias->type);

        $location = $this->locationService->loadLocation($urlAlias->destination);

        $query = new LocationQuery();
        $query->performCount = false;
        $query->filter = new LogicalAnd([
            new Subtree($location->pathString),
            new LogicalNot(new LocationId($location->id)),
        ]);

        $query->limit = 100;

        $results = $this->searchService->findLocations($query)->searchHits;

        $resultCount = count($results);
        $randomInt = random_int(0, $resultCount - 1);

        $location = $results[$randomInt]->valueObject;

        return $this->urlAliasService->reverseLookup($location)->path;
    }
}
