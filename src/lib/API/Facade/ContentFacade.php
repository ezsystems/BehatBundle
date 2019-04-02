<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\Facade;


use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\URLAliasService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\URLAlias;
use EzSystems\BehatBundle\API\ContentData\ContentDataCreator;
use PHPUnit\Framework\Assert;

class ContentFacade
{
    /** @var ContentService  */
    private $contentService;

    /** @var LocationService  */
    private $locationService;

    /** @var URLAliasService  */
    private $urlAliasService;

    /** @var PermissionResolver  */
    private $permissionResolver;

    /** @var UserService  */
    private $userService;

    /** @var ContentTypeService  */
    private $contentTypeService;

    /** @var ContentDataCreator */
    private $contentDataCreator;

    public function __construct(ContentTypeService $contentTypeService, ContentService $contentService, LocationService $locationService, URLAliasService $urlAliasService, PermissionResolver $permissionResolver, UserService $userService, ContentDataCreator $contentDataCreator)
    {
        $this->contentTypeService = $contentTypeService;
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->urlAliasService = $urlAliasService;
        $this->permissionResolver = $permissionResolver;
        $this->userService = $userService;
        $this->contentDataCreator = $contentDataCreator;
    }

    public function setUser(string $username)
    {
        $user = $this->userService->loadUserByLogin($username);
        $this->permissionResolver->setCurrentUserReference($user);
    }


    public function createContent($contentTypeIdentifier, $parentUrl, $language, $contentItemData = null)
    {
        $parentUrlAlias = $this->urlAliasService->lookup($parentUrl);
        Assert::assertEquals(URLAlias::LOCATION,  $parentUrlAlias->type);

        $parentLocationId = $parentUrlAlias->destination;
        $locationCreateStruct = $this->locationService->newLocationCreateStruct($parentLocationId);

        $this->contentDataCreator->setContentTypeIdentifier($contentTypeIdentifier);
        $contentCreateStruct = $contentItemData ? $this->contentDataCreator->getFilledContentDataStruct($contentItemData, $language) : $this->contentDataCreator->getRandomContentData($language);

        $draft = $this->contentService->createContent($contentCreateStruct, [$locationCreateStruct]);
        $this->contentService->publishVersion($draft->versionInfo);
    }
}