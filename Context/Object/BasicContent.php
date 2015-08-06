<?php
/**
  *This file is part of the BehatBundle package
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Context\Object;

use Behat\Gherkin\Node\TableNode;
use PHPUnit_Framework_Assert as Assertion;

/**
 * Sentences for Fields
 */
trait BasicContent
{
    /**
     * @Given a/an :path folder exists
     *
     */
    public function createBasicContent( $path )
    {
        return $this->getBasicContentManager()->createContentwithPath( $path, 'folder' );
    }
}
