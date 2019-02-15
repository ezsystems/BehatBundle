<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use EzSystems\BehatBundle\API\Facade\RoleFacade;

class RoleContext implements Context
{
    /** @var RoleFacade  */
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
        $this->roleFacade->createRole($roleName);
    }

    /**
     * @Given I create a role :roleName with policies
     */
    public function createRoleWithPolicies(string $roleName, TableNode $policies): void
    {
        $this->roleFacade->createRole($roleName);

        foreach ($policies as $policy)
        {
            $this->roleFacade->addPolicyToRole($roleName, $policy);
        }
    }

    /**
     * @Given I add policies to :roleName
     */
    public function addPolicyToRole($roleName, TableNode $policies): void
    {
        foreach ($policies as $policy)
        {
            $this->roleFacade->addPolicyToRole($roleName, $policy);
        }
    }

    /**
     * @Given I add policy :policyName to :roleName with limitations
     */
    public function addPolicyToRoleWithLimitation(string $policyName, $roleName, TableNode $limitations): void
    {
        $parsedLimitations = $this->parseLimitations($limitations);
        $this->roleFacade->addPolicyToRole($roleName, $policyName, $parsedLimitations);
    }

    private function parseLimitations($limitations)
    {
        return null;
    }
}