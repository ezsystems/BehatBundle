<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\Facade;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\RoleService;
use EzSystems\BehatBundle\Context\Api\LimitationParser\LimitationParsersCollector;
use EzSystems\BehatBundle\Context\Api\LimitationParser\LimitationParserInterface;

class RoleFacade
{
    private $roleService;

    /** @var LimitationParsersCollector */
    private $limitationParsersCollector;

    public function __construct(RoleService $roleService, LimitationParsersCollector $limitationParsersCollector)
    {
        $this->roleService = $roleService;
        $this->limitationParsersCollector = $limitationParsersCollector;
    }

    public function createRole($roleName)
    {
        $roleCreateStruct = $this->roleService->newRoleCreateStruct($roleName);
        $roleDraft = $this->roleService->createRole($roleCreateStruct);
        $this->roleService->publishRoleDraft($roleDraft);
    }

    public function addPolicyToRole($roleName, $module, $function, $limitations = null)
    {
        $role = $this->roleService->loadRoleByIdentifier($roleName);
        $roleDraft = $this->roleService->createRoleDraft($role);
        $policyCreateStruct = $this->roleService->newPolicyCreateStruct($module, $function);

        if ($limitations !== null) {
            foreach ($limitations as $limitation) {
                $policyCreateStruct->addLimitation($limitation);
            }
        }

        $updatedRoleDraft = $this->roleService->addPolicyByRoleDraft($roleDraft, $policyCreateStruct);

        $this->roleService->publishRoleDraft($updatedRoleDraft);
    }

    public function roleExist($roleName): bool
    {
        try {
            $this->roleService->loadRoleByIdentifier($roleName);

            return true;
        } catch (NotFoundException $e) {
            return false;
        }
    }

    /**
     * @return LimitationParserInterface[]
     */
    public function getLimitationParsers(): array
    {
        return $this->limitationParsersCollector->getLimitationParsers();
    }
}
