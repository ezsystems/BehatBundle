<?php
/**
 * File containing the Role ObjectManager class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\ObjectManager;

use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\API\Repository\Values\User\RoleCreateStruct;
use eZ\Publish\API\Repository\Exceptions as ApiExceptions;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\Core\Base\Exceptions as CoreExceptions;
use Behat\Gherkin\Node\TableNode;

class Role extends Base
{
    /**
     * Make sure a Role with name $name exists in parent group
     *
     * @param string $name Role identifier
     *
     * @return \eZ\Publish\API\Repository\Values\User\Role
     */
    public function ensureRoleExists( $name )
    {
         /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $that = $this;
        $role = $repository->sudo(
            function() use( $repository, $name, $that )
            {
                $role = null;
                $roleService = $repository->getRoleService();
                try
                {
                    $role = $roleService->loadRoleByIdentifier( $name );
                }
                catch ( ApiExceptions\NotFoundException $e )
                {
                    $roleCreateStruct = $roleService->newRoleCreateStruct( $name );
                    $roleDraft = $roleService->createRole( $roleCreateStruct );
                    $roleService->publishRoleDraft($roleDraft);
                    $role = $roleService->loadRole($roleDraft->id);
                    $that->addObjectToList( $role );
                }

                return $role;
            }
        );

        return $role;
    }

    /**
     * Fetches the role with identifier
     *
     * @param string $identifier Role identifier
     *
     * @return \eZ\Publish\API\Repository\Values\User\Role
     */
    public function getRole( $identifier )
    {
         /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $role = $repository->sudo(
            function() use( $repository, $identifier )
            {
                $role = null;
                $roleService = $repository->getRoleService();
                try
                {
                    $role = $roleService->loadRoleByIdentifier( $identifier );
                }
                catch ( ApiExceptions\NotFoundException $e )
                {
                    // Role not found, do nothing, returns null
                }

                return $role;
            }
        );

        return $role;
    }

    /**
     * [destroy description]
     * @param  ValueObject $object [description]
     * @return [type]              [description]
     */
    protected function destroy( ValueObject $object )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $repository->sudo(
            function() use( $repository, $object )
            {
                $roleService = $repository->getRoleService();
                try
                {
                    $objectToBeRemoved = $roleService->loadRole( $object->id );
                    $roleService->deleteRole( $objectToBeRemoved );
                }
                catch ( ApiExceptions\NotFoundException $e )
                {
                    // nothing to do
                }
            }
        );
    }
}
