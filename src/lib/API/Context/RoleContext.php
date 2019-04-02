<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\API\Repository\Values\User\Limitation\ContentTypeLimitation;
use eZ\Publish\API\Repository\Values\User\Limitation\SubtreeLimitation;
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
        $this->roleFacade->setUser("admin");

        if ($this->roleFacade->doesRoleExist($roleName))
        {
            return;
        }

        $this->roleFacade->createRole($roleName);
    }

    /**
     * @Given I create a role :roleName with policies
     */
    public function createRoleWithPolicies(string $roleName, TableNode $policies): void
    {
        // TODO: Extract it somehow
        $this->roleFacade->setUser("admin");

        if ($this->roleFacade->doesRoleExist($roleName))
        {
            return;
        }

        $this->roleFacade->createRole($roleName);


        foreach ($policies as $policy)
        {
            $this->roleFacade->addPolicyToRole($roleName, $policy['module'], $policy['function']);
        }
    }

    /**
     * @Given I add policies to :roleName
     */
    public function addPolicyToRole($roleName, TableNode $policies): void
    {
        $this->roleFacade->setUser("admin");

        foreach ($policies as $policy)
        {
            $this->roleFacade->addPolicyToRole($roleName, $policy['module'], $policy['function']);
        }
    }

    /**
     * @Given I add policy :module :function to :roleName with limitations
     */
    public function addPolicyToRoleWithLimitation(string $module, string $function, $roleName, TableNode $limitations): void
    {
        $this->roleFacade->setUser("admin");

        $parsedLimitations = $this->parseLimitations($limitations);
        $this->roleFacade->addPolicyToRole($roleName, $module, $function, $parsedLimitations);
    }

    private function parseLimitations(TableNode $limitations)
    {
        $parsedLimitations = [];

        foreach ($limitations->getHash() as $row)
        {
            switch ($row['limitationType']) {
                case Limitation::CONTENTTYPE:
                    $parsedLimitations[] = new ContentTypeLimitation(
                        ['limitationValues' => [1]] // contentTypeId
                    );
                    break;
                case Limitation::SUBTREE:
                    $parsedLimitations[] = new SubtreeLimitation(
                        ['limitationValues' => ['/1/2/']] //pathstring
                    );
                    break;
                default:
                    throw new \Exception('Unsupported limitation');
            }
        }

        return $parsedLimitations;
    }
}