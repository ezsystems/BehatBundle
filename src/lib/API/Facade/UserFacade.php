<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\Facade;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\User\Limitation\RoleLimitation;
use eZ\Publish\API\Repository\Values\User\UserGroup;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use EzSystems\Behat\API\ContentData\FieldTypeData\PasswordProvider;
use EzSystems\Behat\API\ContentData\RandomDataGenerator;

class UserFacade
{
    public const USER_CONTENT_TYPE_IDENTIFIER = 'user';
    public const USERGROUP_CONTENT_IDENTIFIER = 'user_group';
    public const ROOT_USERGROUP_CONTENT_ID = 4;
    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \eZ\Publish\API\Repository\RoleService */
    private $roleService;

    /** @var \eZ\Publish\API\Repository\SearchService */
    private $searchService;

    /**
     * @var \EzSystems\Behat\API\ContentData\RandomDataGenerator
     */
    private $randomDataGenerator;

    public function __construct(UserService $userService, ContentTypeService $contentTypeService, RoleService $roleService, SearchService $searchService, RandomDataGenerator $randomDataGenerator)
    {
        $this->userService = $userService;
        $this->contentTypeService = $contentTypeService;
        $this->roleService = $roleService;
        $this->searchService = $searchService;
        $this->randomDataGenerator = $randomDataGenerator;
    }

    public function createUserGroup(string $groupName)
    {
        $userGroupContentType = $this->contentTypeService->loadContentTypeByIdentifier('user_group');

        $userGroupStruct = $this->userService->newUserGroupCreateStruct('eng-GB', $userGroupContentType);
        $userGroupStruct->setField('name', $groupName);

        $parentGroup = $this->userService->loadUserGroup(self::ROOT_USERGROUP_CONTENT_ID);

        $this->userService->createUserGroup($userGroupStruct, $parentGroup);
    }

    public function createUser($userName, $userLastName, $userGroupName = null, $userEmail = null, $languageCode = 'eng-GB')
    {
        $userCreateStruct = $this->userService->newUserCreateStruct(
            $userName,
            $userEmail == null ? $this->randomDataGenerator->getFaker()->email : $userEmail,
            $this->getDefaultPassword(),
            $languageCode,
            $this->contentTypeService->loadContentTypeByIdentifier(self::USER_CONTENT_TYPE_IDENTIFIER)
        );

        $userCreateStruct->setField('first_name', $userName);
        $userCreateStruct->setField('last_name', $userLastName);

        $parentGroup = null !== $userGroupName ?
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

        if (count($result->searchHits) > 0) {
            $content = $result->searchHits[0]->valueObject;

            return $this->userService->loadUserGroup($content->contentInfo->id);
        }

        return $this->loadLegacyUserGroupByName($userGroupName);
    }

    private function loadLegacyUserGroupByName(string $userGroupName): UserGroup
    {
        // There are some groups that loadUserGroupByName cannot load (missing data in SQL installer)
        // We need to use a broader criterion to find them

        $query = new Query();
        $query->filter = new Criterion\LogicalAnd([
            new Criterion\ContentTypeIdentifier(self::USERGROUP_CONTENT_IDENTIFIER),
        ]);

        $result = $this->searchService->findContent($query);

        foreach ($result->searchHits as $searchHit) {
            /** @var \eZ\Publish\API\Repository\Values\Content\Content $content */
            $content = $searchHit->valueObject;

            if ($content->contentInfo->name === $userGroupName) {
                return $this->userService->loadUserGroup($content->contentInfo->id);
            }
        }

        throw new NotFoundException('User Group', $userGroupName);
    }
}
