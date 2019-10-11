<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\Context\Api;

use Behat\Behat\Context\Context;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;

class TestContext implements Context
{
    private $permissionResolver;
    private $userService;

    /**
     * @injectService $userService @ezpublish.api.service.user
     * @injectService $permissionResolver @eZ\Publish\API\Repository\PermissionResolver
     */
    public function __construct(UserService $userService, PermissionResolver $permissionResolver)
    {
        $this->userService = $userService;
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * @Given I am using the API as :username
     */
    public function iAmLoggedAsUser(string $username)
    {
        $user = $this->userService->loadUserByLogin($username);
        $this->permissionResolver->setCurrentUserReference($user);
    }

    /**
     * @BeforeScenario @admin
     */
    public function loginAdminBeforeScenarioHook()
    {
        $this->iAmLoggedAsUser('admin');
    }
}
