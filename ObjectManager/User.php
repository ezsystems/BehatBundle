<?php
/**
 * File containing the User ObjectManager class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\ObjectManager;

use EzSystems\BehatBundle\ObjectManager\UserGroup as UserGroupManager;
use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\API\Repository\Exceptions as ApiExceptions;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\User\User as APIUser;
use Behat\Gherkin\Node\TableNode;


class User extends Base
{
    const DEFAULT_LANGUAGE              = 'eng-GB';

    /**
     * These values are set by the default eZ Publish installation.
     */
    const USER_IDENTIFIER               = 'user';

    const USERGROUP_ROOT_CONTENT_ID     = 4;
    const USERGROUP_ROOT_LOCATION       = 5;

    /**
     * Load a User Group by id
     *
     * @param int $id User Group content identifier
     *
     * @return \eZ\Publish\API\Repository\Values\User\UserGroup
     */
    protected function loadUserGroup( $id )
    {
        return $this->getContext()->getUserGroupManager()->loadUserGroup( $id );
    }

    /**
     * Search User Groups with given name
     *
     * @param string $name name of User Group to search for
     * @param string $parentLocationId (optional) parent location id to search in
     *
     * @return search results
     */
    public function searchUserGroups( $name, $parentLocationId = null )
    {
        return $this->getContext()->getUserGroupManager()->searchUserGroups( $name, $parentLocationId );
    }

    /**
     * Create new User Group inside existing parent User Group
     *
     * @param string $name  User Group name
     * @param \eZ\Publish\API\Repository\Values\User\UserGroup $parentGroup  (optional) parent user group, defaults to UserGroup "/Users"
     *
     * @return \eZ\Publish\API\Repository\Values\User\UserGroup
     */
    public function createUserGroup( $name, $parentGroup = null )
    {
        return $this->getContext()->getUserGroupManager()->createUserGroup( $name, $parentGroup );
    }

    /**
     * Load a User by id
     *
     * @param int $id User content identifier
     *
     * @return \eZ\Publish\API\Repository\Values\User\User
     */
    public function loadUser( $id )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $userService = $repository->getUserService();

        $user = $repository->sudo(
            function() use( $id, $userService )
            {
                return $userService->loadUser( $id );
            }
        );

        return $user;
    }

    /**
     * Load a User by login
     *
     * @param int $id User content identifier
     *
     * @return \eZ\Publish\API\Repository\Values\User\User
     */
    public function loadUserByLogin( $login )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $userService = $repository->getUserService();

        $user = $repository->sudo(
            function() use( $login, $userService )
            {
                return $userService->loadUserByLogin( $login );
            }
        );

        return $user;
    }

    /**
     * Search User with given username, optionally at given location
     *
     * @param string $username name of User to search for
     * @param string $parentGroupLocationId where to search, in User Group tree
     *
     * @return User found
     */
    public function searchUserByLogin( $username, $parentGroupId = null )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $userService = $repository->getUserService();

        try
        {
            $user = $this->loadUserByLogin( $username );
        }
        catch ( ApiExceptions\NotFoundException $e )
        {
            return null;
        }

        if ( $user && $parentGroupId )
        {
            $userGroups = $repository->sudo(
                function() use ( $user, $userService )
                {
                    return $userService->loadUserGroupsOfUser( $user );
                }
            );

            foreach ( $userGroups as $userGroup )
            {
                if ( $userGroup->getVersionInfo()->getContentInfo()->id == $parentGroupId )
                {
                    return $user;
                }
            }
            // user not found in $parentGroupId
            return null;
        }

        return $user;
    }

    /**
     * Create user inside given User Group; DELETES existing User if login already exists!
     *
     * @param $username username of the user to create
     * @param $email email address of user to create
     * @param $password account password for user to create
     * @param $parentGroup pathstring wherein to create user
     *
     * @return user created
     */
    protected function createUser( $username, $email, $password, $parentGroup = null, $fields = array() )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $userService = $repository->getUserService();

        $userCreateStruct = $userService->newUserCreateStruct( $username, $email, $password, self::DEFAULT_LANGUAGE );
        $userCreateStruct->setField( 'first_name', $username );
        $userCreateStruct->setField( 'last_name', $username );
        foreach ( $fields as $fieldName => $fieldValue )
        {
            $userCreateStruct->setField( $fieldName, $fieldValue );
        }

        $user = $repository->sudo(
            function() use( $username, $userCreateStruct, $parentGroup, $userService )
            {
                try
                {
                    $existingUser = $userService->loadUserByLogin( $username );
                    $userService->deleteUser( $existingUser );
                }
                catch ( NotFoundException $e )
                {
                    // do nothing
                }
                if ( !$parentGroup )
                {
                    $parentGroup = $userService->loadUserGroup( self::USERGROUP_ROOT_CONTENT_ID );
                }
                return $userService->createUser( $userCreateStruct, array( $parentGroup ) );
            }
        );
        $this->addObjectToList( $user );
        return $user;
    }

    /**
     * Update user with given field and value
     *
     * @param $user user to update
     * @param $fieldLabel name of the field to update
     * @param $fieldValue value of the field to update to
     */
    public function updateUser( $user, $fields = array() )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $contentService = $repository->getContentService();
        $userService = $repository->getUserService();

        $userUpdateStruct = $userService->newUserUpdateStruct();
        $contentUpdateStruct = $contentService->newContentUpdateStruct();
        foreach ( $fields as $fieldName => $fieldValue )
        {
            switch ( $fieldName )
            {
                case 'password':
                    // TODO: throw, not impl.
                    break;
                case 'email':
                    // TODO: throw, not impl.
                    break;
                default:
                    $contentUpdateStruct->setField( $fieldName, $fieldValue, 'eng-GB' );
                    break;
            }
        }
        $userUpdateStruct->contentUpdateStruct = $contentUpdateStruct;

        $repository->sudo(
            function() use( $user, $userUpdateStruct, $userService )
            {
                $userService->updateUser( $user, $userUpdateStruct );
            }
        );
    }

    /**
     * Make sure a User with name $username, $email and $password exists in parent group
     *
     * @param string $username User name
     * @param string $email User's email
     * @param string $password User's password
     * @param string $parentGroupName (optional) name of the parent group to check
     *
     * @return \eZ\Publish\API\Repository\Values\User\User
     */
    public function ensureUserExists( $username, $email, $password, $parentGroupName = null )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $userService = $repository->getUserService();

        if ( $parentGroupName )
        {
            $parentSearchHits = $this->searchUserGroups( $parentGroupName );

            // Found matching Group(s)
            if ( !empty( $parentSearchHits ) )
            {
                $firstGroupId = $parentSearchHits[0]->valueObject->contentInfo->id;
                foreach ( $parentSearchHits as $userGroupHit )
                {
                    $groupId = $userGroupHit->valueObject->contentInfo->id;
                    // Search for user in this group
                    $user = $this->searchUserByLogin( $username, $groupId );
                    if ( $user )
                    {
                        return $user;
                    }
                }

                // create user inside existing parent Group, use first group found
                $parentGroup = $this->loadUserGroup( $firstGroupId );
                return $this->createUser( $username, $email, $password, $parentGroup );
            } // else

            // Parent Group does not exist yet, so create it at "root" User Group.
            $rootGroup = $this->loadUserGroup( self::USERGROUP_ROOT_CONTENT_ID );
            $parentGroup = $this->createUserGroup( $parentGroupName, $rootGroup );

            return $this->createUser( $username, $email, $password, $parentGroup );
        }
        // else,

        $user = $this->searchUserByLogin( $username );
        if ( !$user )
        {
            $user = $this->createUser( $username, $email, $password );
        }
        return $user;
    }

    /**
     * Make sure a User with name $username does not exist (in parent group)
     *
     * @param string $username          User name
     * @param string $parentGroupName   (optional) name of the parent group to check
     */
    public function ensureUserDoesntExist( $username, $parentGroupName = null )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();

        //Initialize Services
        $userService = $repository->getUserService();
        $locationService = $repository->getLocationService();

        $user = null;

        if ( $parentGroupName )
        {
            // find matching Parent Group name
            $parentSearchHits = $this->searchUserGroups( $parentGroupName, self::USERGROUP_ROOT_LOCATION );

            if ( !empty( $parentSearchHits ) )
            {
                foreach ( $parentSearchHits as $parentGroupFound )
                {
                    $groupId = $parentGroupFound->valueObject->contentInfo->id;
                    //Search for already existing matching Child user
                    $user = $this->searchUserByLogin( $username, $groupId );
                    if ( $user )
                    {
                        break;
                    }
                }
            }
        }
        else
        {
            try
            {
                $user = $this->loadUserByLogin( $username );
            }
            catch ( ApiExceptions\NotFoundException $e )
            {
                // nothing to do
            }
        }

        if ( $user )
        {
            $repository->sudo(
                function() use( $user, $userService )
                {
                    try
                    {
                        $userService->deleteUser( $user );
                    }
                    catch ( ApiExceptions\NotFoundException $e )
                    {
                        // nothing to do
                    }
                }
            );
        }
    }


    /**
     * Checks if the User with username $username exists
     *
     * @param string $username User name
     * @param string $parentGroupName User group name to search inside
     *
     * @return boolean true if it exists, false if user or group don't exist
     */
    public function checkUserExistenceByUsername( $username, $parentGroupName = null )
    {
        if ( $parentGroupName )
        {
            // find parent group name
            $searchResults = $this->searchUserGroups( $parentGroupName );

            if ( empty( $searchResults ) )
            {
                // group not found, so return immediately
                return false;
            }
            $groupId = $searchResults[0]->valueObject->contentInfo->id;
        }
        else
        {
            $groupId = null;
        }

        $searchResults = $this->searchUserByLogin( $username, $groupId );

        return empty( $searchResults ) ? false : true;
    }

    /**
     * Checks if the User with email $email exists
     *
     * @param string $email User email
     * @param string $parentGroupName User group name to search inside
     *
     * @return boolean true if it exists, false if not
     */
    public function checkUserExistenceByEmail( $email, $parentGroupName = null )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $userService = $repository->getUserService();

        $existingUsers = $userService->loadUsersByEmail( $email );
        if ( count( $existingUsers ) == 0 )
        {
            return false;
        }

        if ( $parentGroupName )
        {
            foreach ( $existingUsers as $user )
            {
                $userGroups = $userService->loadUserGroupsOfUser( $user );
                foreach ( $userGroups as $userGroup )
                {
                    if ( $userGroup->getFieldValue( 'name' ) == $parentGroupName )
                        return true;
                }
            }
        }

        return false;
    }

    /**
     * Checks if the User with $id exists
     *
     * @param string $id Identifier of the possible content
     *
     * @return boolean true if it exists, false if not
     */
    public function checkUserExistenceById( $id )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $userService = $repository->getUserService();

        return $repository->sudo(
            function() use( $id, $userService )
            {
                // attempt to load the user with the id
                try
                {
                    $userService->loadUser( $id );
                    return true;
                }
                catch ( ApiExceptions\NotFoundException $e )
                {
                    return false;
                }
            }
        );
    }

    public function createPasswordHash( $login, $password, $type )
    {
        switch ( $type )
        {
            case 2:
                /* PASSWORD_HASH_MD5_USER */
                return md5( "{$login}\n{$password}" );
            case 3:
                /* PASSWORD_HASH_MD5_SITE */
                $site = null;
                return md5( "{$login}\n{$password}\n{$site}" );
            case 5:
                /* PASSWORD_HASH_PLAINTEXT */
                return $password;
        }
        /* PASSWORD_HASH_MD5_PASSWORD (1) */
        return md5( $password );
    }

    protected function destroy( ValueObject $object )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $repository->sudo(
            function() use( $repository, $object )
            {
                $userService = $repository->getUserService();
                try
                {
                    $objectToBeRemoved = $userService->loadUser( $object->id );
                    $userService->deleteUser( $objectToBeRemoved );
                }
                catch ( ApiExceptions\NotFoundException $e )
                {
                    // nothing to do
                }
            }
        );
    }

}
