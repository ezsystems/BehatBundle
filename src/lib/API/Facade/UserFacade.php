<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\Facade;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\User\UserGroup;
use EzSystems\BehatBundle\API\ContentData\ContentDataProvider;

class UserFacade
{
    private $userService;
    private $contentTypeService;
    private $locationService;
    private $roleService;
    private $searchService;
    private $contentDataProvider;

    private const USER_CONTENT_TYPE_IDENTIFIER = 'user';
    private const ROOT_USERGROUP_CONTENTID = 5;

    public function __construct(UserService $userService, ContentTypeService $contentTypeService, LocationService $locationService, RoleService $roleService, SearchService $searchService, ContentDataProvider $contentDataProvider)
    {
        $this->userService = $userService;
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
        $this->roleService = $roleService;
        $this->searchService = $searchService;
        $this->contentDataProvider = $contentDataProvider;
    }

    public function createUserGroup(string $groupName)
    {
        $userGroupContentType = $this->contentTypeService->loadContentTypeByIdentifier('user_group');

        $userGroupStruct = $this->userService->newUserGroupCreateStruct('eng-GB', $userGroupContentType);
        $userGroupStruct->setField('name', $groupName);

        $location = $this->locationService->loadLocation(5);
        $parentGroup = $this->userService->loadUserGroup($location->contentId);

        $this->userService->createUserGroup($userGroupStruct, $parentGroup);
    }

    public function createUser($userName, $userGroupName = null)
    {
        $languageCode = 'eng-GB';

        $userCreateStruct = $this->userService->newUserCreateStruct(
            $userName,
            $this->contentDataProvider->getFieldData('email', $languageCode),
            $this->contentDataProvider->getFieldData('password', $languageCode),
            $languageCode,
            $this->contentTypeService->loadContentTypeByIdentifier(self::USER_CONTENT_TYPE_IDENTIFIER));

        $userCreateStruct->setField('first_name', $userName);
        $userCreateStruct->setField('last_name', $this->contentDataProvider->getFieldData('ezstring', $languageCode));


        $parentGroup = $userGroupName !== null ?
            $this->loadUserGroupByName($userGroupName) :
            $this->userService->loadUserGroup(self::ROOT_USERGROUP_CONTENTID );

        $this->userService->createUser($userCreateStruct, [$parentGroup]);
    }

    public function assignUserToRole($userName, $roleName)
    {
        $user = $this->userService->loadUserByLogin($userName);
        $role = $this->roleService->loadRoleByIdentifier($roleName);

        $this->roleService->assignRoleToUser($role, $user);
    }

    public function assignUserGroupToRole($userGroupName, $roleName)
    {
        $group = $this->loadUserGroupByName($userGroupName);
        $role = $this->roleService->loadRoleByIdentifier($roleName);

        $this->roleService->assignRoleToUserGroup($role, $group);
    }

    private function loadUserGroupByName(string $userGroupName)
    {
        $query = new Query();
        $query->filter = new Criterion\LogicalAnd([
                new Criterion\Subtree( self::ROOT_USERGROUP_CONTENTID ),
                new Criterion\ContentTypeIdentifier( self::USERGROUP_CONTENT_IDENTIFIER ),
                new Criterion\Field( 'name', Criterion\Operator::EQ, $userGroupName ),
            ]);

        $result = $this->searchService->findContent( $query );
        return $result->searchHits[0];
    }
}