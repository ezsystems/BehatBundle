<?php
/**
 * File containing the ContentTypeGroup sentences interface for BehatBundle.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Sentence\ObjectSentence;

use Behat\Gherkin\Node\TableNode;

interface ContentTypeGroup
{
    /**
     * @Given /^I have (?:a |)Content Type Group with identifier "(?P<identifier>[^"]*)"$/
     * @Given /^there is (?:a |)Content Type Group with identifier "(?P<identifier>[^"]*)"$/
     */
    public function iHaveContentTypeGroup( $identifier );

    /**
     * @Given /^I (?:do not|don\'t) have a Content Type Group with identifier "(?P<identifier>[^"]*)"$/
     * @Given /^there (?:is not|isn\'t) a Content Type Group with identifier "(?P<identifier>[^"]*)"$/
     */
    public function iDonTHaveContentTypeGroup( $identifier );

    /**
     * @Given /^I have (?:the |)following Content Type Groups(?:\:|)$/
     * @Given /^there are the following Content Type Groups(?:\:|)$/
     */
    public function iHaveTheFollowingContentTypeGroups( TableNode $table );

    /**
     * @Then /^Content Type Group with identifier "(?P<identifier>[^"]*)" (?:was|is) (?<action>(?:stored|removed))$/
     */
    public function contentTypeGroupIs( $identifier, $action );

    /**
     * @Then /^Content Type Group with identifier "(?P<identifier>[^"]*)" (?:was|is)(?: not|n\'t) (?<action>(?:stored|removed))$/
     */
    public function contentTypeGroupIsNot( $identifier, $action );

    /**
     * @Then /^only (?P<total>\d+) Content Type Group with identifier "(?P<identifier>[^"]*)" is stored$/
     */
    public function countContentTypeGroup( $total, $identifier );
}
