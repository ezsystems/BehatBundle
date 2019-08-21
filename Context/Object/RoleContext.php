<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\Context\Object;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use EzSystems\BehatBundle\API\Facade\RoleFacade;

class RoleContext implements Context
{
    /** @var RoleFacade */
    private $roleFacade;

    public function __construct(RoleFacade $roleFacade)
    {
        $this->roleFacade = $roleFacade;
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
     */
    public function addPolicyToRole($roleName, TableNode $policies): void
    {
        foreach ($policies as $policy) {
            $this->roleFacade->addPolicyToRole($roleName, $policy['module'], $policy['function']);
        }
    }

    /**
     * @Given I add policy :module :function to :roleName with limitations
     */
    public function addPolicyToRoleWithLimitation(string $module, string $function, $roleName, TableNode $limitations): void
    {
        $parsedLimitations = $this->parseLimitations($limitations);
        $this->roleFacade->addPolicyToRole($roleName, $module, $function, $parsedLimitations);
    }

    private function parseLimitations(TableNode $limitations)
    {
        $parsedLimitations = [];
        $limitationParsers = $this->roleFacade->getLimitationParsers();

        foreach ($limitations->getHash() as $rawLimitation) {
            foreach ($limitationParsers as $parser) {
                if ($parser->supports($rawLimitation['limitationType'])) {
                    $parsedLimitations[] = $parser->parse($rawLimitation['limitationValue']);
                    break;
                }
            }
        }

        return $parsedLimitations;
    }
}
