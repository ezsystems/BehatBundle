<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\Facade;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\URLAliasService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\URLAlias;
use EzSystems\BehatBundle\API\ContentData\ContentDataProvider;
use PHPUnit\Framework\Assert;

class ContentFacade
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\URLAliasService */
    private $urlAliasService;

    /** @var \EzSystems\BehatBundle\API\ContentData\ContentDataProvider */
    private $contentDataProvider;

    public function __construct(ContentService $contentService, LocationService $locationService, URLAliasService $urlAliasService, ContentDataProvider $contentDataProvider)
    {
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->urlAliasService = $urlAliasService;
        $this->contentDataProvider = $contentDataProvider;
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

        $draft = $this->contentService->createContent($contentCreateStruct, [$locationCreateStruct]);

        return $draft;
    }

    public function createContent($contentTypeIdentifier, $parentUrl, $language, $contentItemData = null): Content
    {
        $draft = $this->createContentDraft($contentTypeIdentifier, $parentUrl, $language, $contentItemData);

        return $this->contentService->publishVersion($draft->versionInfo);
    }

    public function editContent($locationURL, $language, $contentItemData)
    {
        $urlAlias = $this->urlAliasService->lookup($locationURL);
        Assert::assertEquals(URLAlias::LOCATION, $urlAlias->type);

        $location = $this->locationService->loadLocation($urlAlias->destination);
        $contentDraft = $this->contentService->createContentDraft($location->getContentInfo());
        $contentUpdateStruct = $this->contentService->newContentUpdateStruct();

        $this->contentDataProvider->setContentTypeIdentifier($contentDraft->getContentType()->identifier);
        $this->contentDataProvider->getFilledContentDataStruct($contentUpdateStruct, $contentItemData, $language);

        $updatedDraft = $this->contentService->updateContent($contentDraft->getVersionInfo(), $contentUpdateStruct);
        $this->contentService->publishVersion($updatedDraft->getVersionInfo());
    }

    public function getContentByLocationURL($locationURL): Content
    {
        $urlAlias = $this->urlAliasService->lookup($locationURL);
        Assert::assertEquals(URLAlias::LOCATION, $urlAlias->type);

        return $this->locationService
            ->loadLocation($urlAlias->destination)
            ->getContent();
    }
}
