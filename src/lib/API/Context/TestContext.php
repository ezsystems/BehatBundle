<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;

class TestContext implements Context
{
    private $permissionResolver;
    private $userService;

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

    /**
     * @BeforeScenario
     */
    public function loginAPIUser(BeforeScenarioScope $scope)
    {
        $tags = $scope->getScenario()->getTags();
        foreach ($tags as $tag) {
            if (0 === strpos($tag, 'APIUser:')) {
                $this->iAmLoggedAsUser(explode(':', $tag)[1]);

                return;
            }
        }
    }
}
