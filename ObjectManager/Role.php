<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\ObjectManager;

use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\API\Repository\Exceptions as ApiExceptions;

/**
 * * @deprecated in 7.0, will be removed in 8.0.
 */
class Role extends Base
{
    /**
     * Make sure a Role with name $name exists in parent group.
     *
     * @param string $name Role identifier
     *
     * @return \eZ\Publish\API\Repository\Values\User\Role
     */
    public function ensureRoleExists($name)
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $that = $this;
        $role = $repository->sudo(
            function () use ($repository, $name, $that) {
                $role = null;
                $roleService = $repository->getRoleService();

                // make sure role name starts with uppercase as this is what default setup provides
                if ($name !== ucfirst($name)) {
                    @trigger_error(
                        "'{$name}' role name should start with uppercase letter",
                        E_USER_DEPRECATED
                    );
                }

                try {
                    $role = $roleService->loadRoleByIdentifier(ucfirst($name));
                } catch (ApiExceptions\NotFoundException $e) {
                    $roleCreateStruct = $roleService->newRoleCreateStruct(ucfirst($name));
                    $roleDraft = $roleService->createRole($roleCreateStruct);
                    $roleService->publishRoleDraft($roleDraft);
                    $role = $roleService->loadRole($roleDraft->id);
                    $that->addObjectToList($role);
                }

                return $role;
            }
        );

        return $role;
    }

    /**
     * Fetches the role with identifier.
     *
     * @param string $identifier Role identifier
     *
     * @return \eZ\Publish\API\Repository\Values\User\Role
     */
    public function getRole($identifier)
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $role = $repository->sudo(
            function () use ($repository, $identifier) {
                $role = null;
                $roleService = $repository->getRoleService();
                try {
                    // make sure role name starts with uppercase as this is what default setup provides
                    if ($identifier !== ucfirst($identifier)) {
                        @trigger_error(
                            "'{$identifier}' role name should start with uppercase letter",
                            E_USER_DEPRECATED
                        );
                    }
                    $role = $roleService->loadRoleByIdentifier(ucfirst($identifier));
                } catch (ApiExceptions\NotFoundException $e) {
                    // Role not found, do nothing, returns null
                }

                return $role;
            }
        );

        return $role;
    }

    /**
     * [destroy description].
     *
     * @param  ValueObject $object [description]
     *
     * @return [type]              [description]
     */
    protected function destroy(ValueObject $object)
    {
        // Ignore warnings about not empty cache directory. See: https://github.com/ezsystems/BehatBundle/pull/71
        $currentErrorReportingLevel = error_reporting();
        error_reporting($currentErrorReportingLevel & ~E_WARNING);

        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $repository->sudo(
            function () use ($repository, $object) {
                $roleService = $repository->getRoleService();
                try {
                    $objectToBeRemoved = $roleService->loadRole($object->id);
                    $roleService->deleteRole($objectToBeRemoved);
                } catch (ApiExceptions\NotFoundException $e) {
                    // nothing to do
                }
            }
        );

        error_reporting($currentErrorReportingLevel);
    }
}
