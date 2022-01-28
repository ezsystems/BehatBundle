<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\Facade;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\URLAliasService;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LocationId;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalNot;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Subtree;
use Ibexa\Contracts\Core\Repository\Values\Content\URLAlias;
use PHPUnit\Framework\Assert;

class SearchFacade
{
    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\URLAliasService */
    private $urlAliasService;

    /** @var \Ibexa\Contracts\Core\Repository\SearchService */
    private $searchService;
    /**
     * @var \Ibexa\Contracts\Core\Repository\ContentService
     */
    private $contentService;

    private const ROOT_LOCATION_ID = 2;

    public function __construct(URLAliasService $urlAliasService, LocationService $locationService, SearchService $searchService, ContentService $contentService)
    {
        $this->urlAliasService = $urlAliasService;
        $this->locationService = $locationService;
        $this->searchService = $searchService;
        $this->contentService = $contentService;
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

    public function getRandomContentIds(int $number)
    {
        $query = new Query();
        $query->limit = 50;
        $query->performCount = false;
        $query->filter = new LogicalNot(new LocationId(self::ROOT_LOCATION_ID));

        $results = $this->searchService->findContent($query)->searchHits;

        $indices = array_rand($results, $number);

        if ($number === 1) {
            return $results[$indices]->valueObject->contentInfo->id;
        }

        $randomContentIDs = [];

        foreach ($indices as $i) {
            $randomContentIDs[] = $results[$i]->valueObject->contentInfo->id;
        }

        return $randomContentIDs;
    }

    public function getRandomLocationID(): int
    {
        $contentId = $this->getRandomContentIds(1);

        return $this->contentService->loadContent($contentId)->contentInfo->mainLocationId;
    }
}
