<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\Facade;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\RoleService;
use EzSystems\Behat\API\Context\LimitationParser\LimitationParsersCollector;

class RoleFacade
{
    /** @var \eZ\Publish\API\Repository\RoleService */
    private $roleService;

    /** @var \EzSystems\Behat\API\Context\LimitationParser\LimitationParsersCollector */
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

        if (null !== $limitations) {
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
     * @return \EzSystems\Behat\API\Context\LimitationParser\LimitationParserInterface[]
     */
    public function getLimitationParsers(): array
    {
        return $this->limitationParsersCollector->getLimitationParsers();
    }
}
