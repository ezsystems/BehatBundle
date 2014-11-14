<?php
/**
 * File containing the User object context
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Context\Object;

use Behat\Gherkin\Node\TableNode;
use PHPUnit_Framework_Assert as Assertion;

/**
 * Sentences for Users
 */
trait User
{
    /**
     * @Given there is a User with name :username
     *
     * @return \eZ\Publish\API\Repository\Values\User\User
     */
    public function iHaveUser( $username )
    {
        $email = $this->findNonExistingUserEmail( $username );
        $password = $username;
        $user = $this->getUserManager()->ensureUserExists( $username, $email, $password );
        $this->addValuesToKeyMap( $email, $user->email );
    }

    /**
     * @Given there is a User with name :username, email :email and password :password
     *
     * @return \eZ\Publish\API\Repository\Values\User\User
     */
    public function iHaveUserWithUsernameEmailAndPassword( $username, $email, $password )
    {
        $this->getUserManager()->ensureUserExists( $username, $email, $password );
    }

    /**
     * @Given there is a User with name :username in :parentGroup group
     *
     * @return \eZ\Publish\API\Repository\Values\User\User
     */
    public function iHaveUserInGroup( $username, $parentGroupName )
    {
        $email = $this->findNonExistingUserEmail( $username );
        $password = $username;
        $user = $this->getUserManager()->ensureUserExists( $username, $email, $password, $parentGroupName );
        $this->addValuesToKeyMap( $email, $user->email );
    }

    /**
     * @Given there is a User with name :username, email :email and password :password in :parentGroup group
     *
     * @return \eZ\Publish\API\Repository\Values\User\User
     */
    public function iHaveUserWithUsernameEmailAndPasswordInGroup( $username, $email, $password, $parentGroupName )
    {
        return $this->getUserManager()->ensureUserExists( $username, $email, $password, $parentGroupName );
    }

    /**
     * @Given there is a User with name :username with the following fields:
     *
     * @return \eZ\Publish\API\Repository\Values\User\User
     */
    public function iHaveUserWithFields( $username, TableNode $table )
    {
        $fieldsTable = $table->getTable();
        array_shift( $fieldsTable );
        $fields = array();
        foreach ( $fieldsTable as $fieldRow )
        {
            $fields[ $fieldRow[0] ] = $fieldRow[1];
        }

        $password = isset( $fields['password'] ) ? $fields['password'] : $username;
        $email = isset( $fields['email'] ) ? $fields['email'] : $this->findNonExistingUserEmail( $username );

        // first, ensure the user exists
        $user = $this->getUserManager()->ensureUserExists( $username, $email, $password );

        // then, update with fields
        $this->getUserManager()->updateUser( $user, $fields );
    }

    /**
     * @Given there isn't a User with name :username
     */
    public function iDontHaveUser( $username )
    {
        $this->getUserManager()->ensureUserDoesntExist( $username );
    }

    /**
     * @Given there isn't a User with name :username in :parentGroup group
     */
    public function iDontHaveUserInGroup( $username, $parentGroup )
    {
        $this->getUserManager()->ensureUserDoesntExist( $username, $parentGroup );
    }

    /**
     * @Given there is a User with id :id
     */
    public function iHaveUserWithId( $id )
    {
        $name = $this->findNonExistingUserName();
        $user = $this->getUserManager()->ensureUserExists( $name );
        $this->addValuesToKeyMap( $id, $user->id );
    }

    /**
     * @Given there isn't a User with id :id
     */
    public function iDontHaveUserWithId( $id )
    {
        $randomId = $this->findNonExistingUserId();
        $this->addValuesToKeyMap( $id, $randomId );
    }

    /**
     * @Given there is a User with name :username with id :id in :parentGroup group
     */
    public function iHaveUserWithIdInGroup( $username, $id, $parentGroup )
    {
        $user = $this->getUserManager()->ensureUserExists( $username, $parentGroup );
        $this->addValuesToMap( $id, $user->id );
    }

    /**
     * @Given there are the following Users:
     */
    public function iHaveTheFollowingUsers( TableNode $table )
    {
        $users = $table->getTable();

        array_shift( $users );
        foreach ( $users as $user )
        {
            // array( [0] => userName, [1] => groupName );
            $this->getUserManager()->ensureUserExists( $user[0], $user[1] );
        }
    }

    /**
     * @Given a User with name :username already exists
     * @Then User with name :username exists
     */
    public function assertUserWithNameExists( $username )
    {
        Assertion::assertTrue(
            $this->getUserManager()->checkUserExistenceByUsername( $username ),
            "Couldn't find User with name '$username'."
        );
    }

    /**
     * @Then User with name :username doesn't exist
     */
    public function assertUserWithNameDoesntExist( $username )
    {
        Assertion::assertFalse(
            $this->getUserManager()->checkUserExistenceByUsername( $username ),
            "User with name '$username' was found."
        );
    }

    /**
     * @Then User with name :username exists in group :parentGroup
     * @Then User with name :username exists in :parentGroup group
     */
    public function assertUserWithNameExistsInGroup( $username, $parentGroup )
    {
        Assertion::assertTrue(
            $this->getUserManager()->checkUserExistenceByUsername( $username, $parentGroup ),
            "Couldn't find User with name '$username' in parent group '$parentGroup'."
        );
    }

    /**
     * @Then User with name :username doesn't exist in group :parentGroup
     * @Then User with name :username doesn't exist in :parentGroup group
     */
    public function assertUserWithNameDoesntExistInGroup( $username, $parentGroup )
    {
        Assertion::assertFalse(
            $this->getUserManager()->checkUserExistenceByUsername( $username, $parentGroup ),
            "User with name '$username' was found in parent group '$parentGroup'."
        );
    }

    /**
     * @Then User with name :username doesn't exist in the following groups:
     */
    public function assertUserWithNameDoesntExistInGroups( $username, TableNode $table )
    {
        $groups = $table->getTable();
        array_shift( $groups );
        foreach ( $groups as $group )
        {
            $parentGroupName = $group[0];
            Assertion::assertFalse(
                $this->getUserManager()->checkUserExistenceByUsername( $username, $parentGroupName ),
                "User with name '$username' was found in parent group '$parentGroupName'."
            );
        }
    }

    /**
     * @Then User with name :username has the following fields:
     * @Then User with name :username exists with the following fields:
     */
    public function assertUserWithNameExistsWithFields( $username, TableNode $table )
    {
        Assertion::assertTrue(
            $this->getUserManager()->checkUserExistenceByUsername( $username ),
            "Couldn't find User with name '$username'."
        );

        $user = $this->getUserManager()->loadUserByLogin( $username );

        $fieldsTable = $table->getTable();
        array_shift( $fieldsTable );
        $updateFields = array();
        foreach ( $fieldsTable as $fieldRow )
        {
            $fieldName = $fieldRow[0];
            $expectedValue = $fieldRow[1];

            switch ( $fieldName )
            {
                case 'email':
                    $fieldValue = $user->email;
                    break;
                case 'password':
                    $fieldValue = $user->passwordHash;
                    $expectedValue = $this->getUserManager()->createPasswordHash( $username, $expectedValue, $user->hashAlgorithm );
                    break;
                default:
                    $fieldValue = $user->getFieldValue( $fieldName );
            }
            Assertion::assertEquals(
                $expectedValue,
                $fieldValue,
                "Field '$fieldName' did not contain expected value '$expectedValue'."
            );
        }
    }

    /**
     * Find a non existing User email
     *
     * @return string A not used email
     *
     * @throws \Exception Possible endless loop
     */
    private function findNonExistingUserEmail( $username = 'User' )
    {
        $email = "${username}@ez.no";
        if ( $this->getUserManager()->checkUserExistenceByEmail( $email ) )
        {
            return $email;
        }

        for ( $i = 0; $i < 20; $i++ )
        {
            $email = 'User#' . rand( 1000, 9999 ) . "@ez.no";
            if ( !$this->getUserManager()->checkUserExistenceByEmail( $email ) )
            {
                return $email;
            }
        }

        throw new \Exception( 'Possible endless loop when attempting to find a new email for User.' );
    }

    /**
     * Find a non existing User name
     *
     * @return string A not used name
     *
     * @throws \Exception Possible endless loop
     */
    private function findNonExistingUserName()
    {
        for ( $i = 0; $i < 20; $i++ )
        {
            $username = 'User#' . rand( 1000, 9999 );
            if ( !$this->getUserManager()->checkUserExistenceByUsername( $username ) )
            {
                return $username;
            }
        }

        throw new \Exception( 'Possible endless loop when attempting to find a new name for User.' );
    }

    /**
     * Find an non existent User id
     *
     * @return int Non existing id
     *
     * @throws \Exception Possible endless loop
     */
    private function findNonExistingUserId()
    {
        for ( $i = 0; $i < 20; $i++ )
        {
            $id = rand( 1000, 9999 );
            if ( !$this->getUserManager()->checkUserExistenceById( $id ) )
            {
                return $id;
            }
        }

        throw new \Exception( 'Possible endless loop when attempting to find a new id for User.' );
    }

}
