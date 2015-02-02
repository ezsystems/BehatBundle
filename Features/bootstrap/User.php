<?php
/**
 * File containing the User context class for BehatBundle.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

use EzSystems\BehatBundle\Context\EzBaseContext;
use EzSystems\BehatBundle\Context\Object;

class User extends EzBaseContext
{
    use Object\User;
}
