<?php
/**
 * File containing the User object context
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Context\Object;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use EzSystems\BehatBundle\Context\RepositoryContext;
use eZ\Publish\API\Repository\Exceptions as ApiExceptions;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Repository;
use PHPUnit_Framework_Assert as Assertion;

/**
 * Sentences for Users
 */
class User implements Context
{
    use RepositoryContext;

    const DEFAULT_LANGUAGE = 'eng-GB';

    /**
     * These values are set by the default eZ Publish installation.
     */
    const USER_IDENTIFIER = 'user';
    const USERGROUP_ROOT_CONTENT_ID = 4;
    const USERGROUP_ROOT_LOCATION = 5;

    /**
     * @var \eZ\Publish\API\Repository\UserService
     */
    private $userService;

    /**
     * @var \eZ\Publish\API\Repository\UserService
     */
    private $contentService;

    /**
     * @var EzSystems\BehatBundle\Context\Object\UserGroup
     */
    private $userGroupContext;


    /**
     * @injectService $repository @ezpublish.api.repository
     * @injectService $userService @ezpublish.api.service.user
     * @injectService $contentService @ezpublish.api.service.content
     */
    public function __construct(
        Repository $repository,
        UserService $userService,
        ContentService $contentService
    ) {
        $this->setRepository($repository);
        $this->userService = $userService;
        $this->contentService = $contentService;
    }

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();
        $this->userGroupContext = $environment->getContext('EzSystems\BehatBundle\Context\Object\UserGroup');
    }

    /**
     * @Given there is a User with name :username
     *
     * Ensures a user with username $username exists, creating a new one if necessary.
     *
     * @return \eZ\Publish\API\Repository\Values\User\User
     */
    public function iHaveUser($username)
    {
        $email = $this->findNonExistingUserEmail($username);
        $password = $username;
        $user = $this->ensureUserExists($username, $email, $password);
    }

    /**
     * @Given there is a User with name :username, email :email and password :password
     *
     * Ensures a user exists with given $username/$email/$password, creating a new one if necessary.
     *
     * @return \eZ\Publish\API\Repository\Values\User\User
     */
    public function iHaveUserWithUsernameEmailAndPassword($username, $email, $password)
    {
        $this->ensureUserExists($username, $email, $password);
    }

    /**
     * @Given there is a User with name :username in :parentGroup group
     *
     * Ensures a user with username $username exists as a child of $parentGroup user group, can create either one.
     *
     * @return \eZ\Publish\API\Repository\Values\User\User
     */
    public function iHaveUserInGroup($username, $parentGroupName)
    {
        $email = $this->findNonExistingUserEmail($username);
        $password = $username;
        $user = $this->ensureUserExists($username, $email, $password, $parentGroupName);
    }

    /**
     * @Given there is a User with name :username, email :email and password :password in :parentGroup group
     *
     * Ensures a user with given $username/$email/$password as a child of $parentGroup user group,
     * can create either one.
     *
     * @return \eZ\Publish\API\Repository\Values\User\User
     */
    public function iHaveUserWithUsernameEmailAndPasswordInGroup($username, $email, $password, $parentGroupName)
    {
        return $this->ensureUserExists($username, $email, $password, $parentGroupName);
    }

    /**
     * @Given there is a User with name :username with the following fields:
     *
     * Ensures a user exists with the values provided in the fields/value table. example:
     *       | Name          | value           |
     *       | email         | testuser@ez.no  |
     *       | password      | testuser        |
     *       | first_name    | Test            |
     *       | last_name     | User            |
     *
     * @return \eZ\Publish\API\Repository\Values\User\User
     */
    public function iHaveUserWithFields($username, TableNode $table)
    {
        $fieldsTable = $table->getTable();
        array_shift($fieldsTable);
        $fields = array();
        foreach ($fieldsTable as $fieldRow) {
            $fields[ $fieldRow[0] ] = $fieldRow[1];
        }

        $password = isset($fields['password']) ? $fields['password'] : $username;
        $email = isset($fields['email']) ? $fields['email'] : $this->findNonExistingUserEmail($username);

        // first, ensure the user exists
        $user = $this->ensureUserExists($username, $email, $password);

        // then, update with fields
        $this->updateUser($user, $fields);
    }

    /**
     * @Given there isn't a User with name :username
     *
     * Makes sure a user with username ':username' doesn't exist, removing it if necessary
     */
    public function iDontHaveUser($username)
    {
        $this->ensureUserDoesntExist($username);
    }

    /**
     * @Given there isn't a User with name :username in :parentGroup group
     *
     * Makes sure a user with username $username doesn't exist as a
     * chield of group $parentGroup, removing it if necessary.
     */
    public function iDontHaveUserInGroup($username, $parentGroup)
    {
        $this->ensureUserDoesntExist($username, $parentGroup);
    }

    /**
     * @Given there is a User with id :id
     *
     * Makes user a user with (mapped) id $id exists
     */
    public function iHaveUserWithId($id)
    {
        $name = $this->findNonExistingUserName();
        $user = $this->ensureUserExists($name);
    }

    /**
     * @Given there isn't a User with id :id
     *
     * Makes user a user with (mapped) id $id does not exist
     */
    public function iDontHaveUserWithId($id)
    {
        $randomId = $this->findNonExistingUserId();
    }

    /**
     * @Given there is a User with name :username with id :id in :parentGroup group
     *
     * Ensures a user with username $username and id $id exists as a child of $parentGroup user group,
     * can create either one.
     */
    public function iHaveUserWithIdInGroup($username, $id, $parentGroup)
    {
        $user = $this->ensureUserExists($username, $parentGroup);
    }

    /**
     * @Given there are the following Users:
     *
     * Make sure that users in the provided table exist in their respective parent group. Example:
     *      | username        | parentGroup      |
     *      | testUser1       | Members          |
     *      | testUser2       | Editors          |
     *      | testUser3       | NewParent        | # Both user and group should be created
     */
    public function iHaveTheFollowingUsers(TableNode $table)
    {
        $users = $table->getTable();

        array_shift($users);
        foreach ($users as $user) {
            // array( [0] => userName, [1] => groupName );
            $this->ensureUserExists($user[0], $user[1]);
        }
    }

    /**
     * @Given a User with name :username already exists
     * @Then User with name :username exists
     *
     * Checks that user $username exists
     */
    public function assertUserWithNameExists($username)
    {
        Assertion::assertTrue(
            $this->checkUserExistenceByUsername($username),
            "Couldn't find User with name '$username'."
        );
    }

    /**
     * @Then User with name :username doesn't exist
     *
     * * Checks that user $username does not exist
     */
    public function assertUserWithNameDoesntExist($username)
    {
        Assertion::assertFalse(
            $this->checkUserExistenceByUsername($username),
            "User with name '$username' was found."
        );
    }

    /**
     * @Then User with name :username exists in group :parentGroup
     * @Then User with name :username exists in :parentGroup group
     *
     * Checks that user $username exists as a child of group $parentGroup
     */
    public function assertUserWithNameExistsInGroup($username, $parentGroup)
    {
        Assertion::assertTrue(
            $this->checkUserExistenceByUsername($username, $parentGroup),
            "Couldn't find User with name '$username' in parent group '$parentGroup'."
        );
    }

    /**
     * @Then User with name :username doesn't exist in group :parentGroup
     * @Then User with name :username doesn't exist in :parentGroup group
     *
     * Checks that user $username does not exist as a child of group $parentGroup
     */
    public function assertUserWithNameDoesntExistInGroup($username, $parentGroup)
    {
        Assertion::assertFalse(
            $this->checkUserExistenceByUsername($username, $parentGroup),
            "User with name '$username' was found in parent group '$parentGroup'."
        );
    }

    /**
     * @Then User with name :username doesn't exist in the following groups:
     *
     * Checks that user $username does not exist in any of the provided groups. Example:
     *      | parentGroup           |
     *      | Partners              |
     *      | Anonymous Users       |
     *      | Editors               |
     *      | Administrator users   |
     */
    public function assertUserWithNameDoesntExistInGroups($username, TableNode $table)
    {
        $groups = $table->getTable();
        array_shift($groups);
        foreach ($groups as $group) {
            $parentGroupName = $group[0];
            Assertion::assertFalse(
                $this->checkUserExistenceByUsername($username, $parentGroupName),
                "User with name '$username' was found in parent group '$parentGroupName'."
            );
        }
    }

    /**
     * @Then User with name :username has the following fields:
     * @Then User with name :username exists with the following fields:
     *
     * Checks that user $username exists with the values provided in the field/value table. example:
     *       | Name          | value           |
     *       | email         | testuser@ez.no  |
     *       | password      | testuser        |
     *       | first_name    | Test            |
     *       | last_name     | User            |
     */
    public function assertUserWithNameExistsWithFields($username, TableNode $table)
    {
        Assertion::assertTrue(
            $this->checkUserExistenceByUsername($username),
            "Couldn't find User with name '$username'."
        );

        $user = $this->loadUserByLogin($username);

        $fieldsTable = $table->getTable();
        array_shift($fieldsTable);
        $updateFields = array();
        foreach ($fieldsTable as $fieldRow) {
            $fieldName = $fieldRow[0];
            $expectedValue = $fieldRow[1];

            switch ($fieldName) {
                case 'email':
                    $fieldValue = $user->email;
                    break;
                case 'password':
                    $fieldValue = $user->passwordHash;
                    $expectedValue = $this
                        ->createPasswordHash($username, $expectedValue, $user->hashAlgorithm);
                    break;
                default:
                    $fieldValue = $user->getFieldValue($fieldName);
            }
            Assertion::assertEquals(
                $expectedValue,
                $fieldValue,
                "Field '$fieldName' did not contain expected value '$expectedValue'."
            );
        }
    }

    /**
     * Load a User Group by id
     *
     * @param int $id User Group content identifier
     *
     * @return \eZ\Publish\API\Repository\Values\User\UserGroup
     */
    protected function loadUserGroup($id)
    {
        return $this->userGroupContext->loadUserGroup($id);
    }

    /**
     * Search User Groups with given name
     *
     * @param string $name name of User Group to search for
     * @param string $parentLocationId (optional) parent location id to search in
     *
     * @return search results
     */
    public function searchUserGroups($name, $parentLocationId = null)
    {
        return $this->userGroupContext->searchUserGroups($name, $parentLocationId);
    }

    /**
     * Create new User Group inside existing parent User Group
     *
     * @param string $name  User Group name
     * @param \eZ\Publish\API\Repository\Values\User\UserGroup $parentGroup  (optional) parent user group
     *
     * @return \eZ\Publish\API\Repository\Values\User\UserGroup
     */
    public function createUserGroup($name, $parentGroup = null)
    {
        return $this->userGroupContext->createUserGroup($name, $parentGroup);
    }

    /**
     * Load a User by id
     *
     * @param int $id User content identifier
     *
     * @return \eZ\Publish\API\Repository\Values\User\User
     */
    public function loadUser($id)
    {
        return $this->userService->loadUser($id);
    }

    /**
     * Load a User by login
     *
     * @param int $id User content identifier
     *
     * @return \eZ\Publish\API\Repository\Values\User\User
     */
    public function loadUserByLogin($login)
    {
        return $this->userService->loadUserByLogin($login);
    }

    /**
     * Search User with given username, optionally at given location
     *
     * @param string $username name of User to search for
     * @param string $parentGroupLocationId where to search, in User Group tree
     *
     * @return User found
     */
    public function searchUserByLogin($username, $parentGroupId = null)
    {
        try {
            $user = $this->loadUserByLogin($username);
        } catch (ApiExceptions\NotFoundException $e) {
            return null;
        }

        if ($user && $parentGroupId) {
            $userGroups = $this->userService->loadUserGroupsOfUser($user);

            foreach ($userGroups as $userGroup) {
                if ($userGroup->getVersionInfo()->getContentInfo()->id == $parentGroupId) {
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
    protected function createUser($username, $email, $password, $parentGroup = null, $fields = array())
    {
        $userCreateStruct = $this->userService->newUserCreateStruct(
            $username,
            $email,
            $password,
            self::DEFAULT_LANGUAGE
        );
        $userCreateStruct->setField('first_name', $username);
        $userCreateStruct->setField('last_name', $username);
        foreach ($fields as $fieldName => $fieldValue) {
            $userCreateStruct->setField($fieldName, $fieldValue);
        }

        try {
            $existingUser = $this->userService->loadUserByLogin($username);
            $this->userService->deleteUser($existingUser);
        } catch (ApiExceptions\NotFoundException $e) {
            // do nothing
        }
        if (!$parentGroup) {
            $parentGroup = $this->userService->loadUserGroup(self::USERGROUP_ROOT_CONTENT_ID);
        }

        return $this->userService->createUser($userCreateStruct, array( $parentGroup ));
    }

    /**
     * Update user with given field and value
     *
     * @param $user user to update
     * @param $fieldLabel name of the field to update
     * @param $fieldValue value of the field to update to
     */
    protected function updateUser($user, $fields = array())
    {
        $userUpdateStruct = $this->userService->newUserUpdateStruct();
        $contentUpdateStruct = $this->contentService->newContentUpdateStruct();
        foreach ($fields as $fieldName => $fieldValue) {
            switch ($fieldName) {
                case 'password':
                    // TODO: throw, not impl.
                    break;
                case 'email':
                    // TODO: throw, not impl.
                    break;
                default:
                    $contentUpdateStruct->setField($fieldName, $fieldValue, 'eng-GB');
                    break;
            }
        }
        $userUpdateStruct->contentUpdateStruct = $contentUpdateStruct;
        $this->userService->updateUser($user, $userUpdateStruct);
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
    protected function ensureUserExists($username, $email, $password, $parentGroupName = null)
    {
        if ($parentGroupName) {
            $parentSearchHits = $this->searchUserGroups($parentGroupName);

            // Found matching Group(s)
            if (!empty($parentSearchHits)) {
                $firstGroupId = $parentSearchHits[0]->valueObject->contentInfo->id;
                foreach ($parentSearchHits as $userGroupHit) {
                    $groupId = $userGroupHit->valueObject->contentInfo->id;
                    // Search for user in this group
                    $user = $this->searchUserByLogin($username, $groupId);
                    if ($user) {
                        return $user;
                    }
                }

                // create user inside existing parent Group, use first group found
                $parentGroup = $this->loadUserGroup($firstGroupId);

                return $this->createUser($username, $email, $password, $parentGroup);
            } // else

            // Parent Group does not exist yet, so create it at "root" User Group.
            $rootGroup = $this->loadUserGroup(self::USERGROUP_ROOT_CONTENT_ID);
            $parentGroup = $this->createUserGroup($parentGroupName, $rootGroup);

            return $this->createUser($username, $email, $password, $parentGroup);
        }
        // else,

        $user = $this->searchUserByLogin($username);
        if (!$user) {
            $user = $this->createUser($username, $email, $password);
        }
        return $user;
    }

    /**
     * Make sure a User with name $username does not exist (in parent group)
     *
     * @param string $username          User name
     * @param string $parentGroupName   (optional) name of the parent group to check
     */
    public function ensureUserDoesntExist($username, $parentGroupName = null)
    {
        $user = null;
        if ($parentGroupName) {
            // find matching Parent Group name
            $parentSearchHits = $this->searchUserGroups($parentGroupName, self::USERGROUP_ROOT_LOCATION);

            if (!empty($parentSearchHits)) {
                foreach ($parentSearchHits as $parentGroupFound) {
                    $groupId = $parentGroupFound->valueObject->contentInfo->id;
                    //Search for already existing matching Child user
                    $user = $this->searchUserByLogin($username, $groupId);
                    if ($user) {
                        break;
                    }
                }
            }
        } else {
            try {
                $user = $this->loadUserByLogin($username);
            } catch (ApiExceptions\NotFoundException $e) {
                // nothing to do
            }
        }

        if ($user) {
            try {
                $this->userService->deleteUser($user);
            } catch (ApiExceptions\NotFoundException $e) {
                // nothing to do
            }
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
    public function checkUserExistenceByUsername($username, $parentGroupName = null)
    {
        if ($parentGroupName) {
            // find parent group name
            $searchResults = $this->searchUserGroups($parentGroupName);

            if (empty($searchResults)) {
                // group not found, so return immediately
                return false;
            }
            $groupId = $searchResults[0]->valueObject->contentInfo->id;
        } else {
            $groupId = null;
        }

        $searchResults = $this->searchUserByLogin($username, $groupId);

        return empty($searchResults) ? false : true;
    }

    /**
     * Checks if the User with email $email exists
     *
     * @param string $email User email
     * @param string $parentGroupName User group name to search inside
     *
     * @return boolean true if it exists, false if not
     */
    public function checkUserExistenceByEmail($email, $parentGroupName = null)
    {
        $existingUsers = $this->userService->loadUsersByEmail($email);
        if (count($existingUsers) == 0) {
            return false;
        }

        if ($parentGroupName) {
            foreach ($existingUsers as $user) {
                $userGroups = $this->userService->loadUserGroupsOfUser($user);
                foreach ($userGroups as $userGroup) {
                    if ($userGroup->getFieldValue('name') == $parentGroupName) {
                        return true;
                    }
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
    public function checkUserExistenceById($id)
    {
        // attempt to load the user with the id
        try {
            $this->userService->loadUser($id);
            return true;
        } catch (ApiExceptions\NotFoundException $e) {
            return false;
        }
    }

    public function createPasswordHash($login, $password, $type)
    {
        switch ($type) {
            case 2:
                /* PASSWORD_HASH_MD5_USER */
                return md5("{$login}\n{$password}");
            case 3:
                /* PASSWORD_HASH_MD5_SITE */
                $site = null;
                return md5("{$login}\n{$password}\n{$site}");
            case 5:
                /* PASSWORD_HASH_PLAINTEXT */
                return $password;
        }
        /* PASSWORD_HASH_MD5_PASSWORD (1) */
        return md5($password);
    }

    /**
     * Find a non existing User email
     *
     * @return string A not used email
     *
     * @throws \Exception Possible endless loop
     */
    private function findNonExistingUserEmail($username = 'User')
    {
        $email = "${username}@ez.no";
        if ($this->checkUserExistenceByEmail($email)) {
            return $email;
        }

        for ($i = 0; $i < 20; $i++) {
            $email = 'User#' . rand(1000, 9999) . "@ez.no";
            if (!$this->checkUserExistenceByEmail($email)) {
                return $email;
            }
        }

        throw new \Exception('Possible endless loop when attempting to find a new email for User.');
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
        for ($i = 0; $i < 20; $i++) {
            $username = 'User#' . rand(1000, 9999);
            if (!$this->checkUserExistenceByUsername($username)) {
                return $username;
            }
        }

        throw new \Exception('Possible endless loop when attempting to find a new name for User.');
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
        for ($i = 0; $i < 20; $i++) {
            $id = rand(1000, 9999);
            if (!$this->checkUserExistenceById($id)) {
                return $id;
            }
        }

        throw new \Exception('Possible endless loop when attempting to find a new id for User.');
    }
}
