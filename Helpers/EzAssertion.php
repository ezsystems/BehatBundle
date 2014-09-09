<?php

namespace EzSystems\BehatBundle\Helpers;

use PHPUnit_Framework_Assert as Assertion;

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
    static function assertSingleElemenet( $search, $element, $pageSection = null, $type = 'element' )
    {
        Assertion::assertNotEmpty( $element, "Couldn't find '$search' $type" );

        $section = ( $pageSection === null ) ? "" : " in '$pageSection' page section";
        Assertion::assertEquals(
            1,
            count( $element ),
            "Unexpectedly found more than 1 '$search' $type" . $section
        );
    }
}
