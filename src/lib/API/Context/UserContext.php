<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\API\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Ibexa\Behat\API\Facade\UserFacade;
use Ibexa\Behat\Core\Behat\ArgumentParser;

class UserContext implements Context
{
    /** @var \Ibexa\Behat\API\Facade\UserFacade */
    private $userFacade;

    /** @var \Ibexa\Behat\Core\Behat\ArgumentParser */
    private $argumentParser;

    public function __construct(UserFacade $userFacade, ArgumentParser $argumentParser)
    {
        $this->userFacade = $userFacade;
        $this->argumentParser = $argumentParser;
    }

    /**
     * @Given I create a user group :userGroupName
     */
    public function createUseGroup(string $userGroupName): void
    {
        $this->userFacade->createUserGroup($userGroupName);
    }

    /**
     * @Given I create a user :userName with last name :userLastName
     * @Given I create a user :userName with last name :userLastName in group :userGroupName
     * @Given I create a user :userName with last name :userLastName with email :userEmail
     * @Given I create a user :userName with last name :userLastName in group :userGroupName with email :userEmail
     */
    public function createUserInGroupWithEmail(string $userName, string $userLastName, string $userGroupName = null, string $userEmail = null): void
    {
        $this->userFacade->createUser($userName, $userLastName, $userGroupName, $userEmail);
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
     * @Given I assign user group :groupName to role :roleName with limitations:
     */
    public function assignUserGroupToRole(string $userGroupName, string $roleName, TableNode $limitationData = null): void
    {
        $parsedLimitations = null === $limitationData ? null : $this->argumentParser->parseLimitations($limitationData);

        if (is_array($parsedLimitations) && count($parsedLimitations) > 1) {
            throw new \Exception('Passed more than one Role assignment limitation!');
        }

        $roleLimitation = $parsedLimitations[0] ?? null;

        $this->userFacade->assignUserGroupToRole($userGroupName, $roleName, $roleLimitation);
    }
}

class_alias(UserContext::class, 'EzSystems\Behat\API\Context\UserContext');
