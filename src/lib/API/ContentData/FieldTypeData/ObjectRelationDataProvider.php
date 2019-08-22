<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\ContentData\FieldTypeData;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\URLAliasService;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\Core\FieldType\Relation\Value;
use EzSystems\Behat\Core\Behat\ArgumentParser;

class ObjectRelationDataProvider implements FieldTypeDataProviderInterface
{
    /** @var SearchService */
    private $searchService;

    /** @var ContentService */
    private $contentService;

    /** @var LocationService */
    private $locationService;

    /** @var URLAliasService */
    private $urlAliasService;

    /** @var ArgumentParser */
    private $argumentParser;

    public function __construct(SearchService $searchService, ContentService $contentService, LocationService $locationSerice, URLAliasService $urlAliasSerivce, ArgumentParser $argumentParser)
    {
        $this->searchService = $searchService;
        $this->contentService = $contentService;
        $this->locationService = $locationSerice;
        $this->urlAliasService = $urlAliasSerivce;
        $this->argumentParser = $argumentParser;
    }

    public function supports(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'ezobjectrelation';
    }

    public function generateData(string $language = 'eng-GB')
    {
        return new Value($this->getRandomContentIds(1));
    }

    protected function getRandomContentIds(int $number)
    {
        $query = new Query();
        $query->limit = 50;

        $results = $this->searchService->findContent($query);

        $contentIDs = array_map(function (SearchHit $result) {
            return $result->valueObject->contentInfo->id;
        }, $results->searchHits);

        $indices = array_rand($contentIDs, $number);

        if ($number === 1) {
            return $contentIDs[$indices];
        }

        $randomContentIDs = [];

        foreach ($indices as $i) {
            $randomContentIDs[] = $contentIDs[$i];
        }

        return $randomContentIDs;
    }

    public function parseFromString(string $value)
    {
        return new Value($this->getContentID($value));
    }

    protected function getContentID(string $locationPath)
    {
        $locationURL = $this->argumentParser->parseUrl($locationPath);
        $urlAlias = $this->urlAliasService->lookup($locationURL);

        $location = $this->locationService->loadLocation($urlAlias->destination);

        return $location->getContentInfo()->id;
    }
}
