<?php
/**
 * File containing the UserGroup ObjectManager class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\ObjectManager;

use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\API\Repository\Exceptions as ApiExceptions;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\Core\Base\Exceptions as CoreExceptions;
use Behat\Gherkin\Node\TableNode;

class UserGroup extends Base
{
    /**
     * These values are hardcoded due to the fact that, on a default
     * eZPublish installation, these values are set as default
     */
    const USERGROUP_ROOT_CONTENT_ID     = 4;
    const USERGROUP_ROOT_SUBTREE        = "/1/5/";
    const USERGROUP_CONTENT_IDENTIFIER  = 'user_group';

    /**
     * Load a User Group by id
     *
     * @param int $id User Group content identifier
     *
     * @return \eZ\Publish\API\Repository\Values\User\UserGroup
     */
    public function loadUserGroup( $id )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $userService = $repository->getUserService();

        $userGroup = $repository->sudo(
            function() use( $id, $userService )
            {
                return $userService->loadUserGroup( $id );
            }
        );

        return $userGroup;
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
        $repository = $this->getRepository();
        $searchService = $repository->getSearchService();

        $criterionArray = array(
            new Criterion\Subtree( self::USERGROUP_ROOT_SUBTREE ),
            new Criterion\ContentTypeIdentifier( self::USERGROUP_CONTENT_IDENTIFIER ),
            new Criterion\Field( 'name', Criterion\Operator::EQ, $name ),
        );
        if ( $parentLocationId )
        {
            $criterionArray[] = new Criterion\ParentLocationId( $parentLocationId );
        }
        $query = new Query();
        $query->filter = new Criterion\LogicalAnd( $criterionArray );

        $result = $repository->sudo(
            function() use( $query, $searchService )
            {
                return $searchService->findContent( $query, array(), false );
            }
        );
        return $result->searchHits;
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
        $repository = $this->getRepository();
        $userService = $repository->getUserService();

        if ( !$parentGroup )
        {
            $parentGroup = $this->loadUserGroup( self::USERGROUP_ROOT_CONTENT_ID );
        }

        $userGroup = $repository->sudo(
            function() use( $name, $parentGroup, $userService )
            {
                $userGroupCreateStruct = $userService->newUserGroupCreateStruct( 'eng-GB' );
                $userGroupCreateStruct->setField( 'name', $name );
                return $userService->createUserGroup( $userGroupCreateStruct, $parentGroup );
            }
        );
        $this->addObjectToList( $userGroup );
        return $userGroup;
    }

    /**
     * Make sure a User Group with name $name exists in parent group
     *
     * @param string $name User Group name
     * @param string $parentGroupName (optional) name of the parent group to check
     *
     * @return \eZ\Publish\API\Repository\Values\User\UserGroup
     */
    public function ensureUserGroupExists( $name, $parentGroupName = null )
    {
        if ( $parentGroupName )
        {
            // find parent group name
            $searchResults = $this->searchUserGroups( $parentGroupName );
            if ( !empty( $searchResults ) )
            {
                $parentGroup = $this->loadUserGroup( $searchResults[0]->valueObject->contentInfo->id );
                $parentGroupLocationId = $searchResults[0]->valueObject->contentInfo->mainLocationId;
            }
            else
            {
                // parent group not found, create it
                $parentGroup = $this->createUserGroup( $parentGroupName );
                $parentGroupLocationId = $parentGroup->getVersionInfo()->getContentInfo()->mainLocationId;
            }
        }
        else
        {
            $parentGroup = null;
            $parentGroupLocationId = null;
        }

        $searchResults = $this->searchUserGroups( $name, $parentGroupLocationId );

        if ( !empty( $searchResults ) )
        {
            // found existing child group, return it
            return $this->loadUserGroup( $searchResults[0]->valueObject->contentInfo->id );
        }
        // else, did not find existing group - create one with given name.
        return $this->createUserGroup( $name, $parentGroup );
    }

    /**
     * Make sure a User Group with name $name doesn't exist in parent group
     *
     * @param string $name name of the User Group to check/remove
     * @param string $parentGroupName (optional) parent group to search in
     */
    public function ensureUserGroupDoesntExist( $name, $parentGroupName = null )
    {
        $repository = $this->getRepository();
        $userService = $repository->getUserService();

        if ( $parentGroupName )
        {
            // find parent group name
            $searchResults = $this->searchUserGroups( $parentGroupName );
            if ( empty( $searchResults ) )
            {
                throw new \Exception( "Could not find parent User Group with name '$name'." );
            }

            $parentGroupLocationId = $searchResults[0]->valueObject->contentInfo->mainLocationId;
        }
        else
        {
            $parentGroupLocationId = null;
        }

        $searchResults = $this->searchUserGroups( $name, $parentGroupLocationId );

        if ( empty( $searchResults ) )
        {
            // no existing User Groups found
            return;
        }
        // else, remove existing groups
        $repository->sudo(
            function() use( $searchResults, $userService )
            {
                foreach ( $searchResults as $searchHit )
                {
                    //Attempt to delete User Group
                    try
                    {
                        $userGroup = $userService->loadUserGroup( $searchHit->valueObject->contentInfo->id );
                        $userService->deleteUserGroup( $userGroup );
                    }
                    catch ( ApiExceptions\NotFoundException $e )
                    {
                        // nothing to do
                    }
                }
            }
        );
    }

    /**
     * Checks if the UserGroup with $id exists
     *
     * @param string $id Identifier of the possible content
     *
     * @return boolean true if it exists, false if not
     */
    public function checkUserGroupExistence( $id )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $userService = $repository->getUserService();

        return $repository->sudo(
            function() use( $id, $userService )
            {
                // attempt to load the content type group with the id
                try
                {
                    $userService->loadUserGroup( $id );
                    return true;
                }
                catch ( ApiExceptions\NotFoundException $e )
                {
                    return false;
                }
            }
        );
    }

    /**
     * Checks if the UserGroup with name $name exists
     *
     * @param string $name User Group name
     *
     * @return boolean true if it exists, false if not
     */
    public function checkUserGroupExistenceByName( $name, $parentGroupName = null )
    {
        if ( $parentGroupName )
        {
            // find parent group name
            $searchResults = $this->searchUserGroups( $parentGroupName );
            if ( empty( $searchResults ) )
            {
                throw new \Exception( "Could not find parent User Group with name '$parentGroupName'." );
            }
            $parentGroup = $this->loadUserGroup( $searchResults[0]->valueObject->contentInfo->id );
            $parentGroupLocationId = $searchResults[0]->valueObject->contentInfo->mainLocationId;
            $searchResults = $this->searchUserGroups( $name, $parentGroupLocationId );
        }
        else
        {
            $searchResults = $this->searchUserGroups( $name );
        }

        return empty( $searchResults ) ? false : true;
    }

    /**
     * [destroy description]
     * @param  ValueObject $object [description]
     * @return [type]              [description]
     */
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
                    $objectToBeRemoved = $userService->loadUserGroup( $object->id );
                    $userService->deleteUserGroup( $objectToBeRemoved );
                }
                catch ( ApiExceptions\NotFoundException $e )
                {
                    // nothing to do
                }
            }
        );
    }

}
