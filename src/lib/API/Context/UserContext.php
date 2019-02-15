<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\Context;

use Behat\Behat\Context\Context;
use EzSystems\BehatBundle\API\Facade\UserFacade;

class UserContext implements Context
{
    private $userFacade;

    public function __construct(UserFacade $userFacade)
    {
        $this->userFacade = $userFacade;
    }

    /**
     * @Given I create a user :userName
     * @Given I create a user :userName in group :userGroupName
     */
    public function createUserInGroup(string $userName, string $userGroupName = null): void
    {
        if ($userGroupName === null)
        {
            $userGroupName = $this->userFacade->createUserGroup();
        }

        $this->userFacade->createUser($userName, $userGroupName);
    }

    /**
     * @Given I assign user :name to role :roleName
     * @Given I assign user group :name to role :roleName
     */
    public function assignUserToRole(string $name, string $roleName): void
    {
        $this->userFacade->assignToRole($name, $roleName);
    }
}