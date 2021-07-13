<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\Facade;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\URLAliasService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\URLAlias;
use EzSystems\Behat\API\ContentData\ContentDataProvider;
use FOS\HttpCacheBundle\CacheManager;
use PHPUnit\Framework\Assert;

class ContentFacade
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\URLAliasService */
    private $urlAliasService;

    /** @var \EzSystems\Behat\API\ContentData\ContentDataProvider */
    private $contentDataProvider;

    /** @var \FOS\HttpCacheBundle\CacheManager */
    private $cacheManager;

    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    public function __construct(
        ContentService $contentService,
        LocationService $locationService,
        URLAliasService $urlAliasService,
        ContentDataProvider $contentDataProvider,
        CacheManager $cacheManager,
        Repository $repository
    ) {
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->urlAliasService = $urlAliasService;
        $this->contentDataProvider = $contentDataProvider;
        $this->cacheManager = $cacheManager;
        $this->repository = $repository;
    }

    public function createContentDraft($contentTypeIdentifier, $parentUrl, $language, $contentItemData = null): Content
    {
        $parentUrlAlias = $this->urlAliasService->lookup($parentUrl);
        Assert::assertEquals(URLAlias::LOCATION, $parentUrlAlias->type);

        $parentLocationId = $parentUrlAlias->destination;
        $locationCreateStruct = $this->locationService->newLocationCreateStruct($parentLocationId);

        $this->contentDataProvider->setContentTypeIdentifier($contentTypeIdentifier);

        $contentCreateStruct = $this->contentDataProvider->getRandomContentData($language);

        if ($contentItemData) {
            $contentCreateStruct = $this->contentDataProvider->getFilledContentDataStruct($contentCreateStruct, $contentItemData, $language);
        }

        return $this->contentService->createContent($contentCreateStruct, [$locationCreateStruct]);
    }

    public function createContent($contentTypeIdentifier, $parentUrl, $language, $contentItemData = null): Content
    {
        $draft = $this->createContentDraft($contentTypeIdentifier, $parentUrl, $language, $contentItemData);

        $publishedContent = $this->contentService->publishVersion($draft->versionInfo);
        $this->flushHTTPcache();

        return $publishedContent;
    }

    public function editContent($locationURL, $language, $contentItemData): Content
    {
        $updatedDraft = $this->createDraftForExistingContent($locationURL, $language, $contentItemData);
        $publishedContent = $this->contentService->publishVersion($updatedDraft->getVersionInfo());
        $this->flushHTTPcache();

        return $publishedContent;
    }

    public function createDraftForExistingContent($locationURL, $language, $contentItemData): Content
    {
        $urlAlias = $this->urlAliasService->lookup($locationURL);
        Assert::assertEquals(URLAlias::LOCATION, $urlAlias->type);

        $location = $this->locationService->loadLocation($urlAlias->destination);
        $contentDraft = $this->contentService->createContentDraft($location->getContentInfo());
        $contentUpdateStruct = $this->contentService->newContentUpdateStruct();

        $this->contentDataProvider->setContentTypeIdentifier($contentDraft->getContentType()->identifier);
        $this->contentDataProvider->getFilledContentDataStruct($contentUpdateStruct, $contentItemData, $language);

        return $this->contentService->updateContent($contentDraft->getVersionInfo(), $contentUpdateStruct);
    }

    public function getContentByLocationURL($locationURL): Content
    {
        return $this->getLocationByLocationURL($locationURL)->getContent();
    }

    public function getLocationByLocationURL($locationURL): Location
    {
        $urlAlias = $this->urlAliasService->lookup($locationURL);
        Assert::assertEquals(URLAlias::LOCATION, $urlAlias->type);

        return $this->repository->sudo(function () use ($urlAlias) {
            return $this->locationService->loadLocation($urlAlias->destination);
        });
    }

    private function flushHTTPcache(): void
    {
        $this->cacheManager->flush();
    }
}
