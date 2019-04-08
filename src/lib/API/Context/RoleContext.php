<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelDictionary;
use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\API\Repository\Values\User\Limitation\ContentTypeLimitation;
use eZ\Publish\API\Repository\Values\User\Limitation\SubtreeLimitation;
use EzSystems\BehatBundle\API\Context\LimitationParser\LimitationParserInterface;
use EzSystems\BehatBundle\API\Facade\RoleFacade;

class RoleContext implements Context
{
    use KernelDictionary;

    /** @var RoleFacade  */
    private $roleFacade;

    /**
     * @Given I create a role :roleName
     */
    public function createRole(string $roleName): void
    {
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
        if ($this->roleFacade->doesRoleExist($roleName)) {
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
        $parsedLimitations = $this->parseLimitations($limitations);
        $this->roleFacade->addPolicyToRole($roleName, $module, $function, $parsedLimitations);
    }

    private function parseLimitations(TableNode $limitations)
    {
        $parsedLimitations = [];
        $limitationParsers = $this->getLimitationParsers();

        foreach ($limitations->getHash() as $rawLimitation)
        {
            foreach ($limitationParsers as $parser)
            {
                if ($parser->canWork($rawLimitation['limitationType']))
                {
                    $limitations[] = $parser->parse($rawLimitation['limitationValue']);
                }
            }
        }

        return $parsedLimitations;
    }

    /**
     * @return LimitationParserInterface[]
     */
    private function getLimitationParsers(): array
    {
        return $this->container->findTaggedServiceIds(LimitationParserInterface::SERVICE_TAG);
    }
}