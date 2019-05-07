<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\Context;

use Behat\Behat\Context\Context;
use EzSystems\Behat\API\Facade\UserFacade;

class UserContext implements Context
{
    private $userFacade;

    public function __construct(UserFacade $userFacade)
    {
        $this->userFacade = $userFacade;
    }

    /**
     * @Given I create a user group :userGroupName
     */
    public function createUseGroup(string $userGroupName): void
    {
        $this->userFacade->createUserGroup($userGroupName);
    }

    /**
     * @Given I create a user :userName
     * @Given I create a user :userName in group :userGroupName
     */
    public function createUserInGroup(string $userName, string $userGroupName = null): void
    {
        $this->userFacade->createUser($userName, $userGroupName);
    }

    /**
     * @Given I assign user :userName to role :roleName
     */
    public function assignUserToRole(string $userName, string $roleName): void
    {
        $this->userFacade->assignUserToRole($userName, $roleName);
    }

    /**
     * @Given I assign user group :groupName to role :roleName
     */
    public function assignUserGroupToRole(string $userGroupName, string $roleName): void
    {
        $this->userFacade->assignUserGroupToRole($userGroupName, $roleName);
    }
}
