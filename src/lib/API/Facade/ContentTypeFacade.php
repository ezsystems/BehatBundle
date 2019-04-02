<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\Facade;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;

class ContentTypeFacade
{
    private $contentTypeService;

    private $userService;

    private $permissionResolver;

    public function __construct(ContentTypeService $contentTypeService, UserService $userService, PermissionResolver $permissionResolver)
    {
        $this->contentTypeService = $contentTypeService;
        $this->userService = $userService;
        $this->permissionResolver = $permissionResolver;
    }

    public function setUser(string $username)
    {
        $user = $this->userService->loadUserByLogin($username);
        $this->permissionResolver->setCurrentUserReference($user);
    }

    public function createContentType($contentTypeName, $contentTypeIdentifier, $contentTypeGroupName, $mainLanguageCode, $fieldDefinitions)
    {
        $contentTypeCreateStruct = $this->contentTypeService->newContentTypeCreateStruct($contentTypeIdentifier);
        $contentTypeCreateStruct->names = [$mainLanguageCode => $contentTypeName];
        $contentTypeCreateStruct->mainLanguageCode = $mainLanguageCode;

        foreach ($fieldDefinitions as $definition) {
            $contentTypeCreateStruct->addFieldDefinition($definition);
        }

        $contentTypeGroup = $this->contentTypeService->loadContentTypeGroupByIdentifier($contentTypeGroupName);

        $contentTypeDraft = $this->contentTypeService->createContentType($contentTypeCreateStruct, [$contentTypeGroup]);
        $this->contentTypeService->publishContentTypeDraft($contentTypeDraft);
    }

    public function isContentTypePresent($contentTypeIdentifier): bool
    {
        try {
            $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
            return true;
        }
        catch (NotFoundException $e)
        {
            return false;
        }
    }
}