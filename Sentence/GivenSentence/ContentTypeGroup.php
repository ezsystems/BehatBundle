<?php
/**
 * File containing the ContentTypeGroup sentences interface for BehatBundle.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Sentence\GivenSentence;

use Behat\Gherkin\Node\TableNode;

interface ContentTypeGroup
{
    /**
     * @Given /^I have (?:a |)Content Type Group with identifier "(?P<identifier>[^"]*)"$/
     */
    public function iHaveContentTypeGroup( $identifier );

    /**
     * @Given /^I (?:do not|don\'t) have a Content Type Group with identifier "(?P<identifier>[^"]*)"$/
     */
    public function iDonTHaveContentTypeGroup( $identifier );

    /**
     * @Given /^I have (?:the |)following Content Type Groups(?:\:|)$/
     */
    public function iHaveTheFollowingContentTypeGroups( TableNode $table );
}
