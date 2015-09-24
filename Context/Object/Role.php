<?php
/**
 * File containing the Role object context
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Context\Object;

use Behat\Gherkin\Node\TableNode;
use PHPUnit_Framework_Assert as Assertion;

/**
 * Sentences for Roles
 */
trait Role
{
    /**
     * @Given a/an :name role exists
     *
     * Ensures a role exists with name ':name', creating a new one if necessary.
     *
     * @return \eZ\Publish\API\Repository\Values\User\Role
     */
    public function iHaveRole( $name )
    {
        return $this->getRoleManager()->ensureRoleExists( $name );
    }

    /**
     * @Then I see that a/an :name role exists
     *
     * Verifies that a role with $name exists.
     *
     */
    public function iSeeRole( $name )
    {
        $role = $this->getRoleManager()->getRole( $name );
        Assertion::assertNotNull(
            $role,
            "Couldn't find Role with name $name"
        );
    }

    /**
     * @Given :name do not have any assigned policies
     * @Given :name do not have any assigned Users and groups
     */
    public function noGroupsAndPolicies($name)
    {
        //nothing needs to be done, because roles are created without policies and groups
    }

    /**
     * @Then I see that a/an :name role does not exists
     *
     * Verifies that a role with $name exists.
     *
     */
    public function iDontSeeRole( $name )
    {
        $role = $this->getRoleManager()->getRole( $name );
        Assertion::assertNull(
            $role,
            "Found Role with name $name"
        );
    }
}
