<?php
/**
 * File containing the ApiContext class for BehatBundle.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Context;

use Behat\Behat\Context\BehatContext;

class ApiContext extends BehatContext
{
    public function __construct()
    {
        // sub contexts
        $this->useContext( 'Common', new CommonContext() );
    }

    /**
     * Get repository
     *
     * @return \eZ\Publish\API\Repository\Repository
     */
    public function getRepository()
    {
        return $this->getSubContext( 'Common' )->getRepository();
    }
}
