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
     */
    public function createBasicFolder( $path )
    {
        $names = explode( '/', $path );
        $name = end( $names );
        $fields = array( 'name' => $name );
        return $this->getBasicContentManager()->createContentwithPath( $path, $fields, 'folder' );
    }

    /**
     * @Given a/an :path article exists
     */
    public function createBasicArticle( $path )
    {
        $names = explode( '/', $path );
        $title = end( $names );
        $intro = '<?xml version="1.0" encoding="utf-8"?><section xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/" xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/"><paragraph>This is a paragraph.</paragraph></section>';
        $fields = array( 'title' => $title, 'intro' => $intro );
        return $this->getBasicContentManager()->createContentwithPath( $path, $fields, 'article' );
    }
}
