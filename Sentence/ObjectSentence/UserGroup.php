<?php
/**
 * File containing the UserGroup sentences interface for BehatBundle.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Sentence\ObjectSentence;

use Behat\Gherkin\Node\TableNode;

interface UserGroup
{
    /**
     * @Given /^I have (?:a |the |)User Group (?:with name |named |)"(?P<name>[^"]*)"$/
     * @Given /^(?:there is |there\'s )(?:a |the |)User Group (?:with name |named |)"(?P<name>[^"]*)"$/
     */
    public function iHaveUserGroup( $name );

    /**
     * @Given /^I (?:don\'t |do not )have (?:a |the |)User Group (?:with name |named |)"(?P<name>[^"]*)"$/
     * @Given /^there (?:is not |isn\'t )(?:a |the |)User Group (?:with name |named |)"(?P<name>[^"]*)"$/
     */
    public function iDontHaveUserGroup( $name );

    /**
     * @Given /^I have (?:a |the |)User Group (?:with name |named |)"(?P<name>[^"]*)" in url "(?P<parentUrl>[^"]*)"$/
     * @Given /^(?:there is |there\'s )(?:a |the |)User Group (?:with name |named |)"(?P<name>[^"]*)" in url "(?P<parentUrl>[^"]*)"$/
     */
    public function iHaveUserGroupInURL( $childGroupName, $parentUrl );

    /**
     * @Given /^I have (?:a |the |)User Group (?:with name |named |)"(?P<childGroupName>[^"]*)" in "(?P<parentGroup>[^"]*)"(?: group|)$/
     * @Given /^(?:there is |there\'s )(?:a |the |)User Group (?:with name |named |)"(?P<childGroupName>[^"]*)" in "(?P<parentGroup>[^"]*)"(?: group|)$/
     */
    public function iHaveUserGroupInGroup( $childGroupName, $parentGroup );

    /**
     * @Given /^I (?:don\'t |do not )have (?:a |the |)User Group (?:with name |named |)"(?P<name>[^"]*)" in url "(?P<parentUrl>[^"]*)"$/
     * @Given /^there (?:is not |isn\'t )(?:a |the |)User Group (?:with name |named |)"(?P<name>[^"]*)" in url "(?P<parentUrl>[^"]*)"$/
     */
    public function iDonTHaveUserGroupInURL( $name, $parentUrl );

    /**
     * @Given /^I (?:don\'t |do not )have (?:a |the |)User Group (?:with name |named |)"(?P<childGroupName>[^"]*)" in "(?P<parentGroup>[^"]*)"(?: group|)$/
     * @Given /^there (?:is not |isn\'t )(?:a |the |)User Group (?:with name |named |)"(?P<childGroupName>[^"]*)" in "(?P<parentGroup>[^"]*)"(?: group|)$/
     */
    public function iDonTHaveUserGroupInGroup( $childGroupName, $parentGroup );

    /**
     * @Given /^I have (?:a |the |)User Group with (?:the |)id "(?P<id>[^"]*)"$/
     * @Given /^(?:there is |there\'s )(?:a |the |)User Group with (?:the |)id "(?P<id>[^"]*)"$/
     */
    public function iHaveUserGroupWithId( $id );

    /**
     * @Given /^I (?:don\'t |do not )have (?:a |the |)User Group with (?:the |)id "(?P<id>[^"]*)"$/
     * @Given /^there (?:is not |isn\'t )(?:a |the |)User Group with (?:the |)id "(?P<id>[^"]*)"$/
     */
    public function iDontHaveUserGroupWithId( $id );

    /**
     * @Given /^I have (?:a |the |)User Group (?:with name |named |)"(?P<childGroupName>[^"]*)" with id "(?P<id>[^"]*)" in "(?P<parentGroup>[^"]*)"(?: group|)$/
     * @Given /^(?:there is |there\'s )(?:a |the |)User Group (?:with name |named |)"(?P<childGroupName>[^"]*)" with id "(?P<id>[^"]*)" in "(?P<parentGroup>[^"]*)"(?: group|)$/
     */
    public function iHaveUserGroupWithIdInGroup( $childGroupName, $id, $parentGroup );

    /**
     * @Given /^I have (?:the |)following User Groups:$/
     * @Given /^there are the following User Groups:$/
     */
    public function iHaveTheFollowingUserGroups( TableNode $table );
}
