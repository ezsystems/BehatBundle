<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\Helper;

use PHPUnit\Framework\Assert;

/**
 * * @deprecated in 7.0, will be removed in 8.0.
 *
 * eZ specific assertions.
 */
class EzAssertion
{
    /**
     * Assert that 1 page element was found.
     *
     * @param string $search Search text
     * @param mixed $element Elements found
     * @param string|null $pageSection Page section
     * @param string|null $type HTML element (ex: link, button, input, ...)
     */
    public static function assertSingleElement($search, $element, $pageSection = null, $type = 'element')
    {
        $section = ($pageSection === null) ? '' : " in '$pageSection' page section";
        Assert::assertNotEmpty($element, "Couldn't find '$search' $type" . $section);
        Assert::assertEquals(
            1,
            \count($element),
            "Unexpectedly found more than 1 '$search' $type" . $section
        );
    }

    /**
     * Assert that at least 1 page element was found.
     *
     * @param string $search Search text
     * @param mixed $element Elements found
     * @param string|null $pageSection Page section
     * @param string|null $type HTML element (ex: link, button, input, ...)
     */
    public static function assertElementFound($search, $element, $pageSection = null, $type = 'element')
    {
        $section = ($pageSection === null) ? '' : " in '$pageSection' page section";
        Assert::assertNotEmpty($element, "Couldn't find '$search' $type" . $section);
    }

    /**
     * Assert that no page element was found.
     *
     * @param string $search Search text
     * @param mixed $element Elements found
     * @param string|null $pageSection Page section
     * @param string|null $type HTML element (ex: link, button, input, ...)
     */
    public static function assertElementNotFound($search, $element, $pageSection = null, $type = 'element')
    {
        $section = ($pageSection === null) ? '' : " in '$pageSection' page section";
        Assert::assertEmpty($element, "Found '$search' $type" . $section);
    }
}
