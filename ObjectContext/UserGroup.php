<?php
/**
 * File containing the UserGroup context class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\ObjectContext;

use EzSystems\BehatBundle\Sentence\ObjectSentence\UserGroup as UserGroupObjectSentence;
use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\API\Repository\Exceptions as ApiExceptions;
use Behat\Gherkin\Node\TableNode;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

class UserGroup extends Base implements UserGroupObjectSentence
{
    /**
     * These values are hardcoded due to the fact that, on a default
     * eZPublish installation, these values are set as default
     */
    const USERGROUP_ROOT_LOCATION = 5;
    const USERGROUP_ROOT_OBJ = 4;
    const IDENTIFIER_USER_GROUP = 'user_group';
    const USER_ACCOUNTS_ROOT = "/Users/";

    /**
     * Search User Groups in given location (pathstring) with given name
     * @param string $where pathstring to search in
     * @param string $what name of User Group to search for
     * @return array of User Groups found
     */
    protected function searchUserGroups( $where, $what )
    {
        $query = new Query();

        $criterion1 = new Criterion\Subtree( $where );
        $criterion2 = new Criterion\ContentTypeIdentifier( self::IDENTIFIER_USER_GROUP );
        $criterion3 = new Criterion\Field( 'name', Criterion\Operator::EQ, $what );

        $query->criterion = new Criterion\LogicalAnd(
            array( $criterion1, $criterion2, $criterion3 )
        );
        $results = $this->getRepository()->getSearchService()->findContent( $query );
        return $results->searchHits;
    }

    /**
     * Create User Group inside given User Group
     * @param string $name pathstring inside which to create User Group
     * @param string $parent name of the usergroup to create
     * @param UserService $userService User Service used for user operations
     * @return User Group created
     */
    protected function makeUserGroup( $name, $parent, $userService )
    {
        $userGroupCreateStruct = $userService->newUserGroupCreateStruct( 'eng-GB' );
        $userGroupCreateStruct->setField( 'name', $name );
        return $userService->createUserGroup( $userGroupCreateStruct, $parent );
    }

    /**
     * Given I have a User Group named "<childGroupName>" in "<parentGroup>" group
     * Given there is a User Group named "<childGroupName>" in "<parentGroup>" group
     */
    public function iHaveUserGroupInGroup( $childGroupName, $parentGroup )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();

        $newUserGroup = $repository->sudo(
            function() use( $childGroupName, $parentGroup, $repository )
            {
                //Initialize services
                $userService = $repository->getUserService();
                $locationService = $repository->getLocationService();

                //Search for already existing matching Parent Groups
                $parentSearchHits = $this->searchUserGroups( $locationService->loadLocation( self::USERGROUP_ROOT_LOCATION )->pathString, $parentGroup );

                //Found matching Parent Groups
                if ( !empty( $parentSearchHits ) )
                {
                    $foundParentIndexes = array();
                    foreach ( $parentSearchHits as $parentGroupFound )
                    {
                        $groupLocationId = $parentGroupFound->valueObject->contentInfo->mainLocationId;
                        //Search for already existing matching Child Groups
                        $childrenSearchHits = $this->searchUserGroups( $locationService->loadLocation( $groupLocationId )->pathString, $childGroupName );

                        //Found matching Child Groups
                        if ( !empty( $childrenSearchHits ) )
                        {
                            //First one found suffices, no need to search more
                            return $childrenSearchHits[0]->valueObject->contentInfo->id;
                        }
                        //Did not find matching Child Groups, therefore, saves empty Parents found
                        $foundParentIndexes[] = $parentGroupFound;
                    }
                    //Create Child Group inside ALREADY EXISTING empty Parent Group
                    //Use first (empty) Parent found, to create group inside
                    $parentGroupObjectId = $foundParentIndexes[0]->valueObject->contentInfo->id;
                    $parentGroupObject = $userService->loadUserGroup( $parentGroupObjectId );
                    return $this->makeUserGroup( $childGroupName, $parentGroupObject, $userService );
                }

                //Did not find any matching Parent Groups

                //Parent Group does not exist yet; create it here
                $parentForParent = $userService->loadUserGroup( self::USERGROUP_ROOT_OBJ );
                $parentGroupObject = $this->makeUserGroup( $parentGroup, $parentForParent, $userService );

                //Add created parent object to Array of Created Objects, too
                $this->createdObjects[] = $parentGroupObject;

                //Child Group does not exist yet; create it here (inside former Parent Group)
                return $this->makeUserGroup( $childGroupName, $parentGroupObject, $userService );
            }
        );

        if ( !is_int( $newUserGroup ) )
        {
            $this->createdObjects[] = $newUserGroup;
            return $newUserGroup->versionInfo->contentInfo->id;
        }
        return $newUserGroup;
    }

    /**
     * Given I don\'t have a User Group named "<$childGroupName>" in "<parentGroup>" group
     * Given there isn\'t a User Group named "<childGroupName>" in "<parentGroup>" group
     */
    public function iDonTHaveUserGroupInGroup( $childGroupName, $parentGroup )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();

        $repository->sudo(
            function() use( $childGroupName, $parentGroup, $repository )
            {
                //Initialize Services
                $userService = $repository->getUserService();
                $locationService = $repository->getLocationService();

                //Search for existing matching Parent Groups
                $parentSearchHits = $this->searchUserGroups( $locationService->loadLocation( self::USERGROUP_ROOT_LOCATION )->pathString, $parentGroup );

                //Found matching Parent Groups
                if ( !empty( $parentSearchHits ) )
                {
                    foreach ( $parentSearchHits as $parentGroupFound )
                    {
                        $groupLocationId = $parentGroupFound->valueObject->contentInfo->mainLocationId;

                        //Search for already existing matching Child Groups
                        $childrenSearchHits = $this->searchUserGroups( $locationService->loadLocation( $groupLocationId )->pathString, $childGroupName );

                        //Found matching Child Groups
                        if ( !empty( $childrenSearchHits ) )
                        {
                            foreach ( $childrenSearchHits as $searchHit )
                            {
                                //Attempt to delete User Group
                                try
                                {
                                    $deletableGroupId = $searchHit->valueObject->contentInfo->id;
                                    $deletableGroup = $userService->loadUserGroup( $deletableGroupId );
                                    $userService->deleteUserGroup( $deletableGroup );
                                }
                                catch ( ApiExceptions\NotFoundException $e )
                                {
                                    // nothing to do
                                }
                            }
                        }
                    }
                }
            }
        );
    }

    /**
     * Given I have a User Group with id "<id>"
     * Given there is a User Group with id "<id>"
     */
    public function iHaveUserGroupWithId( $id )
    {
        //'ug' for user group
        $groupId = $this->iHaveUserGroup( 'ctg' . rand( 10000, 99999 ) );
        $this->getMainContext()->getSubContext( 'Common' )->addValuesToMap( $id, $groupId );
    }

    /**
     * Given I don\'t have a User Group with the id "<id>"
     * Given there isn\'t a User Group with the id "<id>"
     */
    public function iDontHaveUserGroupWithId( $id )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();

        $randId = rand( 100, 999 );

        $repository->sudo(
            function() use( $randId, $repository )
            {
                //Initialize Services
                $userService = $repository->getUserService();

                //Attempt to delete the User Group with the identifier
                try
                {
                    $userGroupToRemove = $userService->loadUserGroup( $randId );
                    $userService->deleteUserGroup( $userGroupToRemove );
                }
                catch ( ApiExceptions\NotFoundException $e )
                {
                    // nothing to do
                }
            }
        );
        $this->getMainContext()->getSubContext( 'Common' )->addValuesToMap( $id, $randId );
    }

    /**
     * Given I have a User Group named "<childGroupName>" with id "<id>" in "<parentGroup>" group
     * Given there is a User Group named "<childGroupName>" with id "<id>" in "<parentGroup>" group
     */
    public function iHaveUserGroupWithIdInGroup( $childGroupName, $id, $parentGroup )
    {
        $groupId = $this->iHaveUserGroupInGroup( $childGroupName, $parentGroup );
        $this->getMainContext()->getSubContext( 'Common' )->addValuesToMap( $id, $groupId );
    }

    /**
     * Given I have the following User Groups:
     * Given there are the following User Groups:
     */
    public function iHaveTheFollowingUserGroups( TableNode $table )
    {
        $groups = $table->getNumeratedRows();
        array_shift( $groups );
        foreach ( $groups as $group )
        {
            $this->iHaveUserGroupInGroup( $group[0], $group[1] );
        }
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

    /**
     * Given I have a User Group named "<name>"
     * Given there is a User Group named "<name>"
     */
    public function iHaveUserGroup( $name )
    {
        $this->iHaveUserGroupInURL( $name, self::USER_ACCOUNTS_ROOT );
    }

    /**
     * Given I don\'t have a User Group named "<name>"
     * Given there isn\'t a User Group named "<name>"
     */
    public function iDontHaveUserGroup( $name )
    {
        $this->iDonTHaveUserGroupInURL( $name, self::USER_ACCOUNTS_ROOT );
    }

    /**
     * Given I have a User Group named "<name>" in url "<parentUrl>"
     * Given there is a User Group named "<name>" in url "<parentUrl>"
     */
    public function iHaveUserGroupInURL( $name, $parentUrl )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();

        $newUserGroup = $repository->sudo(
            function() use( $name, $parentUrl, $repository )
            {
                //Initialize Services
                $userService = $repository->getUserService();
                $urlAliasService = $repository->getURLAliasService();
                $locationService = $repository->getLocationService();
                $searchService = $repository->getSearchService();

                //Get Parent User Group
                $parentUrlAlias = $urlAliasService->lookup( $parentUrl );
                $parentLocationId = $parentUrlAlias->destination;
                $parentId = $locationService->loadLocation( $parentLocationId )->getContentInfo()->id;
                $parentGroup = $userService->loadUserGroup( $parentId );

                //Search for already existing User Group
                $query = new Query();
                $query->filter = new Criterion\Field( 'name', Criterion\Operator::EQ, $name );
                $result = $searchService->findContent( $query );

                //User Group already exists
                if ( !empty ($result->searchHits ) )
                {
                    return $result->searchHits[0]->valueObject->contentInfo->id;
                }

                //User Group does not exist yet; therefore create it
                else {
                    return $this->makeUserGroup( $name, $parentGroup, $userService );
                }
            }
        );

        if ( !is_int( $newUserGroup ) )
        {
            $this->createdObjects[] = $newUserGroup;
            return $newUserGroup->versionInfo->contentInfo->id;
        }
        return $newUserGroup;
    }

    /**
     * Given I don\'t have a User Group named "<name>" in url "<parentUrl>"
     * Given there isn\'t a User Group named "<name>" in url "<parentUrl>"
     */
    public function iDonTHaveUserGroupInURL( $name, $parentUrl )
    {
         /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $repository->sudo(
            function() use( $name, $parentUrl, $repository )
            {
                //Initialize Services
                $userService = $repository->getUserService();
                $urlAliasService = $repository->getURLAliasService();
                $locationService = $repository->getLocationService();

                //Get parent User Group
                $parentUrlAlias = $urlAliasService->lookup( $parentUrl );
                $parentLocationId = $parentUrlAlias->destination;

                //Search existing User Group, to delete
                $searchHits = $this->searchUserGroups( $locationService->loadLocation( $parentLocationId )->pathString, $name );

                if ( !empty( $searchHits ) )
                {
                    foreach ( $searchHits as $searchHit )
                    {
                        try
                        {
                            $deletableGroupdId = $searchHit->valueObject->contentInfo->id;
                            $deletableGroup = $userService->loadUserGroup( $deletableGroupdId );
                            $userService->deleteUserGroup( $deletableGroup );
                        }
                        catch ( ApiExceptions\NotFoundException $e )
                        {
                            // nothing to do
                        }
                    }
                }
            }
        );
    }
}
