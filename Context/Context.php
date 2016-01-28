<?php
/**
 * File containing the Base Context class for EzBehatBundle.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Context;

use Behat\Behat\Context\Context as BehatContext;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\Repository\Values\User\UserReference;

/**
 * EzBehat Base Context
 */
class Context implements BehatContext
{
    /**
     * Default Administrator user id
     */
    const ADMIN_USER_ID = 14;

    /**
     * Default language code
     */
    const DEFAULT_LANGUAGE = 'eng-GB';

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @injectService $repository @ezpublish.api.repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @BeforeScenario
     */
    public function loginAdmin($event)
    {
        $this->repository->setCurrentUser(new UserReference(self::ADMIN_USER_ID));
    }
}
