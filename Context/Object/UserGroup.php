<?php
/**
 * File containing the UserGroup object context
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Context\Object;

use Behat\Gherkin\Node\TableNode;
use PHPUnit_Framework_Assert as Assertion;

/**
 * Sentences for UserGroups
 */
trait UserGroup
{
    /**
     * @Given there is a User Group with name :name
     *
     * @return \eZ\Publish\API\Repository\Values\User\UserGroup
     */
    public function iHaveUserGroup( $name )
    {
        return $this->getUserGroupManager()->ensureUserGroupExists( $name );
    }

    /**
     * @Given there isn't a User Group with name :name
     */
    public function iDontHaveUserGroup( $name )
    {
        $this->getUserGroupManager()->ensureUserGroupDoesntExist( $name );
    }

    /**
     * @Given there is a User Group with name :childGroupName in :parentGroup group
     *
     * @return \eZ\Publish\API\Repository\Values\User\UserGroup
     */
    public function iHaveUserGroupInGroup( $childGroupName, $parentGroupName )
    {
        return $this->getUserGroupManager()->ensureUserGroupExists( $childGroupName, $parentGroupName );
    }

    /**
     * @Given there isn't a User Group with name :childGroupName in :parentGroup group
     */
    public function iDonTHaveUserGroupInGroup( $childGroupName, $parentGroup )
    {
        return $this->getUserGroupManager()->ensureUserGroupDoesntExist( $childGroupName, $parentGroup );
    }

    /**
     * @Given there is a User Group with id :id
     */
    public function iHaveUserGroupWithId( $id )
    {
        $name = $this->findNonExistingUserGroupName();

        $userGroup = $this->getUserGroupManager()->ensureUserGroupExists( $name );
        $this->addValuesToKeyMap( $id, $userGroup->id );
    }

    /**
     * @Given there isn't a User Group with id :id
     */
    public function iDontHaveUserGroupWithId( $id )
    {
        $randomId = $this->findNonExistingUserGroupId();

        $this->addValuesToKeyMap( $id, $randomId );
    }

    /**
     * @Given there is a User Group with name :name with id :id in :parentGroup group
     */
    public function iHaveUserGroupWithIdInGroup( $name, $id, $parentGroup )
    {
        $userGroup = $this->getUserGroupManager()->ensureUserGroupExists( $name, $parentGroup );
        $this->addValuesToMap( $id, $userGroup->id );
    }

    /**
     * @Given there are the following User Groups:
     */
    public function iHaveTheFollowingUserGroups( TableNode $table )
    {
        $userGroups = $table->getTable();

        array_shift( $userGroups );
        foreach ( $userGroups as $userGroup )
        {
            $this->getUserGroupManager()->ensureUserGroupExists( $userGroup[0], $userGroup[1] );
        }
    }

    /**
     * @Given a User Group with name :name already exists
     * @Then User Group with name :name exists
     */
    public function assertUserGroupWithNameExists( $name )
    {
        Assertion::assertTrue(
            $this->getUserGroupManager()->checkUserGroupExistenceByName( $name ),
            "Couldn't find UserGroup with name '$name'."
        );
    }

    /**
     * @Then User Group with name :name doesn't exist
     */
    public function assertUserGroupWithNameDoesntExist( $name )
    {
        Assertion::assertFalse(
            $this->getUserGroupManager()->checkUserGroupExistenceByName( $name ),
            "UserGroup with name '$name' was found."
        );
    }

    /**
     * @Then User Group with name :name exists in group :parentGroup
     * @Then User Group with name :name exists in :parentGroup group
     */
    public function assertUserGroupWithNameExistsInGroup( $name, $parentGroup )
    {
        Assertion::assertTrue(
            $this->getUserGroupManager()->checkUserGroupExistenceByName( $name, $parentGroup ),
            "Couldn't find UserGroup with name '$name' in parent group '$parentGroup'."
        );
    }

    /**
     * @Then User Group with name :name doesn't exist in group :parentGroup
     * @Then User Group with name :name doesn't exist in :parentGroup group
     */
    public function assertUserGroupWithNameDoesntExistInGroup( $name, $parentGroup )
    {
        Assertion::assertFalse(
            $this->getUserGroupManager()->checkUserGroupExistenceByName( $name, $parentGroup ),
            "UserGroup with name '$name' was found in parent group '$parentGroup'."
        );
    }

    /**
     * Find an non existent UserGroup id
     *
     * @return int Non existing id
     *
     * @throws \Exception Possible endless loop
     */
    private function findNonExistingUserGroupId()
    {
        for ( $i = 0; $i < 20; $i++ )
        {
            $id = rand( 1000, 9999 );
            if ( !$this->getUserGroupManager()->checkUserGroupExistence( $id ) )
            {
                return $id;
            }
        }

        throw new \Exception( 'Possible endless loop when attempting to find a new id for UserGroup.' );
    }

    /**
     * Find a non existing UserGroup name
     *
     * @return string A not used name
     *
     * @throws \Exception Possible endless loop
     */
    private function findNonExistingUserGroupName()
    {
        for ( $i = 0; $i < 20; $i++ )
        {
            $name = 'UserGroup#' . rand( 1000, 9999 );
            if ( !$this->getUserGroupManager()->checkUserGroupExistenceByName( $name ) )
            {
                return $name;
            }
        }

        throw new \Exception( 'Possible endless loop when attempting to find a new name for UserGroup.' );
    }
}
