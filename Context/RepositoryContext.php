<?php
/**
 * File containing the Repository Context class for EzBehatBundle.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Context;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\Repository\Values\User\UserReference;

/**
 * Repository Context Trait
 */
trait RepositoryContext
{
    /**
     * Default Administrator user id
     */
    private $adminUserId = 14;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param Repository $repository
     */
    public function setRepository(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @BeforeScenario
     */
    public function loginAdmin($event)
    {
        $this->repository->setCurrentUser(new UserReference($this->adminUserId));
    }
}
