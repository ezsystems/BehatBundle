<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\API\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Ibexa\Behat\API\Facade\RoleFacade;
use Ibexa\Behat\Core\Behat\ArgumentParser;

class RoleContext implements Context
{
    /** @var \Ibexa\Behat\API\Facade\RoleFacade */
    private $roleFacade;

    /** @var \Ibexa\Behat\Core\Behat\ArgumentParser */
    private $argumentParser;

    public function __construct(RoleFacade $roleFacade, ArgumentParser $argumentParser)
    {
        $this->roleFacade = $roleFacade;
        $this->argumentParser = $argumentParser;
    }

    /**
     * @Given I create a role :roleName
     */
    public function createRole(string $roleName): void
    {
        if ($this->roleFacade->roleExist($roleName)) {
            return;
        }

        $this->roleFacade->createRole($roleName);
    }

    /**
     * @Given I create a role :roleName with policies
     */
    public function createRoleWithPolicies(string $roleName, TableNode $policies): void
    {
        if ($this->roleFacade->roleExist($roleName)) {
            return;
        }

        $this->roleFacade->createRole($roleName);

        foreach ($policies as $policy) {
            $this->roleFacade->addPolicyToRole($roleName, $policy['module'], $policy['function']);
        }
    }

    /**
     * @Given I add policies to :roleName
     *
     * @param mixed $roleName
     */
    public function addPolicyToRole($roleName, TableNode $policies): void
    {
        foreach ($policies as $policy) {
            $this->roleFacade->addPolicyToRole($roleName, $policy['module'], $policy['function']);
        }
    }

    /**
     * @Given I add policy :module :function to :roleName with limitations
     *
     * @param mixed $roleName
     */
    public function addPolicyToRoleWithLimitation(string $module, string $function, $roleName, TableNode $limitations): void
    {
        $parsedLimitations = $this->argumentParser->parseLimitations($limitations);
        $this->roleFacade->addPolicyToRole($roleName, $module, $function, $parsedLimitations);
    }
}

class_alias(RoleContext::class, 'EzSystems\Behat\API\Context\RoleContext');
