<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\Facade;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\User\Limitation\RoleLimitation;
use eZ\Publish\API\Repository\Values\User\UserGroup;
use EzSystems\Behat\API\ContentData\ContentDataProvider;
use EzSystems\Behat\API\ContentData\FieldTypeData\PasswordProvider;

class UserFacade
{
    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \eZ\Publish\API\Repository\RoleService */
    private $roleService;

    /** @var \eZ\Publish\API\Repository\SearchService */
    private $searchService;

    /** @var \EzSystems\Behat\API\ContentData\ContentDataProvider */
    private $contentDataProvider;

    public const USER_CONTENT_TYPE_IDENTIFIER = 'user';
    public const USERGROUP_CONTENT_IDENTIFIER = 'user_group';
    public const ROOT_USERGROUP_CONTENT_ID = 4;

    public function __construct(UserService $userService, ContentTypeService $contentTypeService, RoleService $roleService, SearchService $searchService, ContentDataProvider $contentDataProvider)
    {
        $this->userService = $userService;
        $this->contentTypeService = $contentTypeService;
        $this->roleService = $roleService;
        $this->searchService = $searchService;
        $this->contentDataProvider = $contentDataProvider;
    }

    public function createUserGroup(string $groupName)
    {
        $userGroupContentType = $this->contentTypeService->loadContentTypeByIdentifier('user_group');

        $userGroupStruct = $this->userService->newUserGroupCreateStruct('eng-GB', $userGroupContentType);
        $userGroupStruct->setField('name', $groupName);

        $parentGroup = $this->userService->loadUserGroup(self::ROOT_USERGROUP_CONTENT_ID);

        $this->userService->createUserGroup($userGroupStruct, $parentGroup);
    }

    public function createUser($userName, $userLastName, $userGroupName = null, $languageCode = 'eng-GB')
    {
        $userCreateStruct = $this->userService->newUserCreateStruct(
            $userName,
            $this->contentDataProvider->getRandomFieldData('email', $languageCode),
            $this->getDefaultPassword(),
            $languageCode,
            $this->contentTypeService->loadContentTypeByIdentifier(self::USER_CONTENT_TYPE_IDENTIFIER));

        $userCreateStruct->setField('first_name', $userName);
        $userCreateStruct->setField('last_name', $userLastName);

        $parentGroup = $userGroupName !== null ?
            $this->loadUserGroupByName($userGroupName) :
            $this->userService->loadUserGroup(self::ROOT_USERGROUP_CONTENT_ID);

        $this->userService->createUser($userCreateStruct, [$parentGroup]);
    }

    public function assignUserToRole($userName, $roleName)
    {
        $user = $this->userService->loadUserByLogin($userName);
        $role = $this->roleService->loadRoleByIdentifier($roleName);

        $this->roleService->assignRoleToUser($role, $user);
    }

    public function assignUserGroupToRole($userGroupName, $roleName, ?RoleLimitation $roleLimitation = null)
    {
        $group = $this->loadUserGroupByName($userGroupName);
        $role = $this->roleService->loadRoleByIdentifier($roleName);

        $this->roleService->assignRoleToUserGroup($role, $group, $roleLimitation);
    }

    public function getDefaultPassword(): string
    {
        return PasswordProvider::DEFAUlT_PASSWORD;
    }

    private function loadUserGroupByName(string $userGroupName): UserGroup
    {
        $query = new Query();
        $query->filter = new Criterion\LogicalAnd([
                new Criterion\ContentTypeIdentifier(self::USERGROUP_CONTENT_IDENTIFIER),
                new Criterion\Field('name', Criterion\Operator::EQ, $userGroupName),
            ]);

        $result = $this->searchService->findContent($query);

        /** @var \eZ\Publish\API\Repository\Values\Content\Content $content */
        $content = $result->searchHits[0]->valueObject;

        return $this->userService->loadUserGroup($content->contentInfo->id);
    }
}
