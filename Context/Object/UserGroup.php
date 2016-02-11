<?php
/**
 * File containing the UserGroup object context
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Context\Object;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use EzSystems\BehatBundle\Context\RepositoryContext;
use eZ\Publish\API\Repository\Exceptions as ApiExceptions;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use PHPUnit_Framework_Assert as Assertion;

/**
 * Sentences for UserGroups
 */
class UserGroup implements Context
{
    use RepositoryContext;

    /**
     * These values are hardcoded due to the fact that, on a default
     * eZPublish installation, these values are set as default
     */
    const USERGROUP_ROOT_CONTENT_ID = 4;
    const USERGROUP_ROOT_SUBTREE = "/1/5/";
    const USERGROUP_CONTENT_IDENTIFIER = 'user_group';

    /**
     * @var \eZ\Publish\API\Repository\UserService
     */
    private $userService;

    /**
     * @var \eZ\Publish\API\Repository\SearchService
     */
    private $searchService;

    /**
     * @injectService $repository @ezpublish.api.repository
     * @injectService $userService @ezpublish.api.service.user
     * @injectService $searchService @ezpublish.api.service.search
     */
    public function __construct(
        Repository $repository,
        UserService $userService,
        SearchService $searchService
    ) {
        $this->setRepository($repository);
        $this->userService = $userService;
        $this->searchService = $searchService;
    }

    /**
     * @Given there is a User Group with name :name
     *
     * Ensures a user group exists with name ':name', creating a new one if necessary.
     *
     * @return \eZ\Publish\API\Repository\Values\User\UserGroup
     */
    public function iHaveUserGroup($name)
    {
        return $this->ensureUserGroupExists($name);
    }

    /**
     * @Given there isn't a User Group with name :name
     *
     * Ensures a user group with name ':name' does not exist, removing it if necessary.
     */
    public function iDontHaveUserGroup($name)
    {
        $this->ensureUserGroupDoesntExist($name);
    }

    /**
     * @Given there is a User Group with name :childGroupName in :parentGroup group
     *
     * Ensures a user group with name ':childGroupName' exists as a child of group ':parentGroup'.
     * If parent group is not found, it is also created.
     *
     * @return \eZ\Publish\API\Repository\Values\User\UserGroup
     */
    public function iHaveUserGroupInGroup($childGroupName, $parentGroupName)
    {
        return $this->ensureUserGroupExists($childGroupName, $parentGroupName);
    }

    /**
     * @Given there isn't a User Group with name :childGroupName in :parentGroup group
     *
     * Ensures a user group with name ':childGroupName' does not exist as a child of group ':parentGroup'.
     * If parent group is not found, an exception is thrown
     */
    public function iDonTHaveUserGroupInGroup($childGroupName, $parentGroup)
    {
        return $this->ensureUserGroupDoesntExist($childGroupName, $parentGroup);
    }

    /**
     * @Given there is a User Group with id :id
     *
     * Creates a new user group with a non-existent name, and maps it's id to ':id'
     */
    public function iHaveUserGroupWithId($id)
    {
        $name = $this->findNonExistingUserGroupName();

        $userGroup = $this->ensureUserGroupExists($name);
        $this->addValuesToKeyMap($id, $userGroup->id);
    }

    /**
     * @Given there isn't a User Group with id :id
     *
     * Maps id ':id' to a non-existent user group.
     */
    public function iDontHaveUserGroupWithId($id)
    {
        $randomId = $this->findNonExistingUserGroupId();

        $this->addValuesToKeyMap($id, $randomId);
    }

    /**
     * @Given there is a User Group with name :name with id :id in :parentGroup group
     *
     * Ensures a user group with name ':name' exists as a child of group ':parentGroup', mapping it's id to ':id'
     */
    public function iHaveUserGroupWithIdInGroup($name, $id, $parentGroup)
    {
        $userGroup = $this->ensureUserGroupExists($name, $parentGroup);
        $this->addValuesToMap($id, $userGroup->id);
    }

    /**
     * @Given there are the following User Groups:
     *
     * Make sure that user groups in the provided table exist in their respective parent group. Example:
     *       | childGroup      | parentGroup      |
     *       | testUserGroup1  | Members          | # should create.
     *       | testUserGroup1  | Editors          | # should create.
     *       | testUserGroup3  | Test Parent      | # parent and child should be created.
     *       | innerGroup3-1   | testUserGroup3   | # should be created inside previous.
     */
    public function iHaveTheFollowingUserGroups(TableNode $table)
    {
        $userGroups = $table->getTable();

        array_shift($userGroups);
        foreach ($userGroups as $userGroup) {
            $this->ensureUserGroupExists($userGroup[0], $userGroup[1]);
        }
    }

    /**
     * @Given a User Group with name :name already exists
     * @Then User Group with name :name exists
     *
     * Checks that a user group with name ':name' exists
     */
    public function assertUserGroupWithNameExists($name)
    {
        Assertion::assertTrue(
            $this->checkUserGroupExistenceByName($name),
            "Couldn't find UserGroup with name '$name'."
        );
    }

    /**
     * @Then User Group with name :name doesn't exist
     *
     * Checks that a user group with name ':name' does not exist
     */
    public function assertUserGroupWithNameDoesntExist($name)
    {
        Assertion::assertFalse(
            $this->checkUserGroupExistenceByName($name),
            "UserGroup with name '$name' was found."
        );
    }

    /**
     * @Then User Group with name :name exists in group :parentGroup
     * @Then User Group with name :name exists in :parentGroup group
     *
     * Checks that a user group with name ':name' exists as a child of ':parentGroup'
     */
    public function assertUserGroupWithNameExistsInGroup($name, $parentGroup)
    {
        Assertion::assertTrue(
            $this->checkUserGroupExistenceByName($name, $parentGroup),
            "Couldn't find UserGroup with name '$name' in parent group '$parentGroup'."
        );
    }

    /**
     * @Then User Group with name :name doesn't exist in group :parentGroup
     * @Then User Group with name :name doesn't exist in :parentGroup group
     *
     * Checks that a user group with name ':name' does not exist as a child of ':parentGroup'
     */
    public function assertUserGroupWithNameDoesntExistInGroup($name, $parentGroup)
    {
        Assertion::assertFalse(
            $this->checkUserGroupExistenceByName($name, $parentGroup),
            "UserGroup with name '$name' was found in parent group '$parentGroup'."
        );
    }

    /**
     * Load a User Group by id
     *
     * @param int $id User Group content identifier
     *
     * @return \eZ\Publish\API\Repository\Values\User\UserGroup
     */
    public function loadUserGroup($id)
    {
        return $this->userService->loadUserGroup($id);
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
        $criterionArray = array(
            new Criterion\Subtree(self::USERGROUP_ROOT_SUBTREE),
            new Criterion\ContentTypeIdentifier(self::USERGROUP_CONTENT_IDENTIFIER),
            new Criterion\Field('name', Criterion\Operator::EQ, $name),
        );
        if ($parentLocationId) {
            $criterionArray[] = new Criterion\ParentLocationId($parentLocationId);
        }
        $query = new Query();
        $query->filter = new Criterion\LogicalAnd($criterionArray);

        $result = $this->searchService->findContent($query, array(), false);

        return $result->searchHits;
    }

    /**
     * Create new User Group inside existing parent User Group
     *
     * @param string $name  User Group name
     * @param \eZ\Publish\API\Repository\Values\User\UserGroup $parentGroup  (optional) parent user group,
     * defaults to UserGroup "/Users"
     *
     * @return \eZ\Publish\API\Repository\Values\User\UserGroup
     */
    public function createUserGroup($name, $parentGroup = null)
    {
        if (!$parentGroup) {
            $parentGroup = $this->userService->loadUserGroup(self::USERGROUP_ROOT_CONTENT_ID);
        }

        $userGroupCreateStruct = $this->userService->newUserGroupCreateStruct('eng-GB');
        $userGroupCreateStruct->setField('name', $name);
        return $this->userService->createUserGroup($userGroupCreateStruct, $parentGroup);
    }

    /**
     * Make sure a User Group with name $name exists in parent group
     *
     * @param string $name User Group name
     * @param string $parentGroupName (optional) name of the parent group to check
     *
     * @return \eZ\Publish\API\Repository\Values\User\UserGroup
     */
    public function ensureUserGroupExists($name, $parentGroupName = null)
    {
        if ($parentGroupName) {
            // find parent group name
            $searchResults = $this->searchUserGroups($parentGroupName);
            if (!empty($searchResults)) {
                $parentGroup = $this->userService->loadUserGroup($searchResults[0]->valueObject->contentInfo->id);
                $parentGroupLocationId = $searchResults[0]->valueObject->contentInfo->mainLocationId;
            } else {
                // parent group not found, create it
                $parentGroup = $this->createUserGroup($parentGroupName);
                $parentGroupLocationId = $parentGroup->getVersionInfo()->getContentInfo()->mainLocationId;
            }
        } else {
            $parentGroup = null;
            $parentGroupLocationId = null;
        }
        $searchResults = $this->searchUserGroups($name, $parentGroupLocationId);
        if (!empty($searchResults)) {
            // found existing child group, return it
            return $this->userService->loadUserGroup($searchResults[0]->valueObject->contentInfo->id);
        }
        // else, did not find existing group - create one with given name.
        return $this->createUserGroup($name, $parentGroup);
    }

    /**
     * Make sure a User Group with name $name doesn't exist in parent group
     *
     * @param string $name name of the User Group to check/remove
     * @param string $parentGroupName (optional) parent group to search in
     */
    protected function ensureUserGroupDoesntExist($name, $parentGroupName = null)
    {
        if ($parentGroupName) {
            // find parent group name
            $searchResults = $this->searchUserGroups($parentGroupName);
            if (empty($searchResults)) {
                throw new \Exception("Could not find parent User Group with name '$name'.");
            }

            $parentGroupLocationId = $searchResults[0]->valueObject->contentInfo->mainLocationId;
        } else {
            $parentGroupLocationId = null;
        }

        $searchResults = $this->searchUserGroups($name, $parentGroupLocationId);

        if (empty($searchResults)) {
            // no existing User Groups found
            return;
        }
        // else, remove existing groups
        foreach ($searchResults as $searchHit) {
            //Attempt to delete User Group
            try {
                $userGroup = $this->userService->loadUserGroup($searchHit->valueObject->contentInfo->id);
                $this->userService->deleteUserGroup($userGroup);
            } catch (ApiExceptions\NotFoundException $e) {
                // nothing to do
            }
        }
    }

    /**
     * Checks if the UserGroup with $id exists
     *
     * @param string $id Identifier of the possible content
     *
     * @return boolean true if it exists, false if not
     */
    protected function checkUserGroupExistence($id)
    {
        // attempt to load the user group with the id
        try {
            $this->userService->loadUserGroup($id);
            return true;
        } catch (ApiExceptions\NotFoundException $e) {
            return false;
        }
    }

    /**
     * Checks if the UserGroup with name $name exists
     *
     * @param string $name User Group name
     *
     * @return boolean true if it exists, false if not
     */
    protected function checkUserGroupExistenceByName($name, $parentGroupName = null)
    {
        if ($parentGroupName) {
            // find parent group name
            $searchResults = $this->searchUserGroups($parentGroupName);
            if (empty($searchResults)) {
                throw new \Exception("Could not find parent User Group with name '$parentGroupName'.");
            }
            $parentGroup = $this->userService->loadUserGroup($searchResults[0]->valueObject->contentInfo->id);
            $parentGroupLocationId = $searchResults[0]->valueObject->contentInfo->mainLocationId;
            $searchResults = $this->searchUserGroups($name, $parentGroupLocationId);
        } else {
            $searchResults = $this->searchUserGroups($name);
        }

        return empty($searchResults) ? false : true;
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
        for ($i = 0; $i < 20; $i++) {
            $id = rand(1000, 9999);
            if (!$this->getUserGroupManager()->checkUserGroupExistence($id)) {
                return $id;
            }
        }

        throw new \Exception('Possible endless loop when attempting to find a new id for UserGroup.');
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
        for ($i = 0; $i < 20; $i++) {
            $name = 'UserGroup#' . rand(1000, 9999);
            if (!$this->getUserGroupManager()->checkUserGroupExistenceByName($name)) {
                return $name;
            }
        }

        throw new \Exception('Possible endless loop when attempting to find a new name for UserGroup.');
    }
}
