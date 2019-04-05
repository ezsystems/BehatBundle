<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\Facade;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\User\UserGroup;

class UserFacade
{
    private $userService;
    private $permissionResolver;
    private $contentTypeService;
    private $locationService;
    private $roleService;

    public function __construct(UserService $userService, PermissionResolver $permissionResolver, ContentTypeService $contentTypeService, LocationService $locationService, RoleService $roleService)
    {
        $this->userService = $userService;
        $this->permissionResolver = $permissionResolver;
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
        $this->roleService = $roleService;
    }

    public function setUser(string $username)
    {
        $user = $this->userService->loadUserByLogin($username);
        $this->permissionResolver->setCurrentUserReference($user);
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

        if ($userGroupName !== null) {
            $parentGroup = $this->loadUserGroupByName($userGroupName);
        }
        else {
            $location = $this->locationService->loadLocation(5);
            $parentGroup = $this->userService->loadUserGroup($location->contentId);
        }

        $userContentType = $this->contentTypeService->loadContentTypeByIdentifier('user');
        // TODO: USE FAKER HERE
        $userCreateStruct = $this->userService->newUserCreateStruct($userName, sprintf('%s@ez.no', $userName), 'Passw0rd-42', 'eng-GB', $userContentType);
        $userCreateStruct->setField('first_name', $userName);
        $userCreateStruct->setField('last_name', 'The first');
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

    private function loadUserGroupByName(string $userGroupName): UserGroup
    {
        $location = $this->locationService->loadLocation(5);
        $parentGroup = $this->userService->loadUserGroup($location->contentId);

        $groups = $this->userService->loadSubUserGroups($parentGroup);

        foreach ($groups as $group)
        {
            if ($group->getName() === $userGroupName)
            {
                return $group;
            }
        }


        /**
         * Search User Groups with given name
         *
         * @param string $name name of User Group to search for
         * @param string $parentLocationId (optional) parent location id to search in
         *
         * @return search results
         */
//        public function searchUserGroups( $name, $parentLocationId = null )
//    {
//        $repository = $this->getRepository();
//        $searchService = $repository->getSearchService();
//
//        $criterionArray = array(
//            new Criterion\Subtree( self::USERGROUP_ROOT_SUBTREE ),
//            new Criterion\ContentTypeIdentifier( self::USERGROUP_CONTENT_IDENTIFIER ),
//            new Criterion\Field( 'name', Criterion\Operator::EQ, $name ),
//        );
//        if ( $parentLocationId )
//        {
//            $criterionArray[] = new Criterion\ParentLocationId( $parentLocationId );
//        }
//        $query = new Query();
//        $query->filter = new Criterion\LogicalAnd( $criterionArray );
//
//        $result = $repository->sudo(
//            function() use( $query, $searchService )
//            {
//                return $searchService->findContent( $query, array(), false );
//            }
//        );
//        return $result->searchHits;
//    }
    }
}