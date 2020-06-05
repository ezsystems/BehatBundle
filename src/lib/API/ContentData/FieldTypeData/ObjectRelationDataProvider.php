<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\ContentData\FieldTypeData;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\URLAliasService;
use EzSystems\Behat\API\Facade\SearchFacade;
use EzSystems\Behat\Core\Behat\ArgumentParser;
use eZ\Publish\Core\FieldType\Relation\Value;

class ObjectRelationDataProvider implements FieldTypeDataProviderInterface
{
    /** @var ContentService */
    private $contentService;

    /** @var LocationService */
    private $locationService;

    /** @var URLAliasService */
    private $urlAliasService;

    /** @var ArgumentParser */
    private $argumentParser;

    /** @var SearchFacade */
    protected $searchFacade;

    public function __construct(SearchFacade $searchFacade, ContentService $contentService, LocationService $locationSerice, URLAliasService $urlAliasSerivce, ArgumentParser $argumentParser)
    {
        $this->contentService = $contentService;
        $this->locationService = $locationSerice;
        $this->urlAliasService = $urlAliasSerivce;
        $this->argumentParser = $argumentParser;
        $this->searchFacade = $searchFacade;
    }

    public function supports(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'ezobjectrelation';
    }

    public function generateData(string $contentTypeIdentifier, string $fieldIdentifier, string $language = 'eng-GB')
    {
        return new Value($this->searchFacade->getRandomContentIds(1));
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
