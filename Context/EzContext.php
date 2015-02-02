<?php
/**
 * File containing the master class for BehatBundle.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Context;

use EzSystems\BehatBundle\Context\EzBaseContext;

/**
 * EzContext has all the needed traits that are globaly used in contexts
 */
class EzContext extends EzBaseContext
{
    use Object\User;
    use Object\UserGroup;
    use Object\Content;
    use Object\ContentTypeGroup;
}
