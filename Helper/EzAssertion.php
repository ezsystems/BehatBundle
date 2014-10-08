<?php
/**
 * File containing the EzAssertions for BehatBundle
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Helper;

use PHPUnit_Framework_Assert as Assertion;

/**
 * eZ specific assertions
 */
class EzAssertion
{
    /**
     * Assert that 1 page element was found
     *
     * @param string $search Search text
     * @param mixed $element Elements found
     * @param null|string $pageSection Page section
     * @param null|string $type HTML element (ex: link, button, input, ...)
     * 
     * @return void
     */
    static function assertSingleElement( $search, $element, $pageSection = null, $type = 'element' )
    {
        $section = ( $pageSection === null ) ? "" : " in '$pageSection' page section";
        Assertion::assertNotEmpty( $element, "Couldn't find '$search' $type" . $section );
        Assertion::assertEquals(
            1,
            count( $element ),
            "Unexpectedly found more than 1 '$search' $type" . $section
        );
    }

    /**
     * Assert that at least 1 page element was found
     *
     * @param string $search Search text
     * @param mixed $element Elements found
     * @param null|string $pageSection Page section
     * @param null|string $type HTML element (ex: link, button, input, ...)
     * 
     * @return void
     */
    static function assertElementFound( $search, $element, $pageSection = null, $type = 'element' )
    {
        $section = ( $pageSection === null ) ? "" : " in '$pageSection' page section";
        Assertion::assertNotEmpty( $element, "Couldn't find '$search' $type" . $section );
    }
}
