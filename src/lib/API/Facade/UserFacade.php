<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\Facade;

use eZ\Publish\API\Repository\UserService;

class UserFacade
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function createUserGroup()
    {
    }

    public function createUser($userName, $userGroupName)
    {
    }

    public function assignToRole($userName, $roleName)
    {
    }
}