<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\API\ContentData\FieldTypeData;

use Ibexa\Behat\API\Facade\SearchFacade;
use Ibexa\Behat\Core\Behat\ArgumentParser;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\URLAliasService;
use Ibexa\Core\FieldType\Relation\Value;

class ObjectRelationDataProvider implements FieldTypeDataProviderInterface
{
    /** @var \Ibexa\Behat\API\Facade\SearchFacade */
    protected $searchFacade;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\URLAliasService */
    private $urlAliasService;

    /** @var \Ibexa\Behat\Core\Behat\ArgumentParser */
    private $argumentParser;

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
        return 'ezobjectrelation' === $fieldTypeIdentifier;
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

class_alias(ObjectRelationDataProvider::class, 'EzSystems\Behat\API\ContentData\FieldTypeData\ObjectRelationDataProvider');
