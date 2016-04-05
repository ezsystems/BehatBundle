<?php
/**
 * File containing the Authentication class for Browser contexts.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Context\Browser\SubContext;

/**
 * Authentication methods
 */
trait Authentication
{
    /**
     * @Given I am logged (in) as a(n) :role
     * @Given I have :role permissions
     *
     * Logs in a (new) user with the role identified by ':role' assigned.
     *
     * @deprecated deprecated since version 6.3.0
     */
    public function iAmLoggedInAsAn( $role )
    {
        trigger_error(
            "iAmLoggedInAsAn is deprecated since v6.3.0 and will be removed in v7.0.0",
            E_USER_DEPRECATED
        );

        if ( $role == 'Anonymous' )
        {
            $this->iAmNotLoggedIn();
        }
        else
        {
            $credentials = $this->getCredentialsFor( $role );
            $this->iAmLoggedInAsWithPassword( $credentials['login'], $credentials['password'] );
        }
    }

    /**
     * @Given I am logged in as :user with password :password
     *
     * Performs the login action with username ':user' and password ':password'.
     * Checks that the resulting page is the homepage.
     *
     * @deprecated deprecated since version 6.3.0
     */
    public function iAmLoggedInAsWithPassword( $user, $password )
    {
        trigger_error(
            "iAmLoggedInAsWithPassword is deprecated since v6.3.0 and will be removed in v7.0.0",
            E_USER_DEPRECATED
        );

        $this->iAmOnPage( 'login' );
        $this->fillFieldWithValue( 'Username', $user );
        $this->fillFieldWithValue( 'Password', $password );
        $this->iClickAtButton( 'Login' );
        $this->iShouldBeOnPage( 'home' );
    }

    /**
     * @Given I am not logged in
     * @Given I don't have permissions
     *
     * Perform the logout action, checks that the resulting page is the homepage.
     *
     * @deprecated deprecated since version 6.3.0
     */
    public function iAmNotLoggedIn()
    {
        trigger_error(
            "iAmLoggedInAsWithPassword is deprecated since v6.3.0 and will be removed in v7.0.0",
            E_USER_DEPRECATED
        );

        $this->iAmOnPage( 'logout' );
        $this->iShouldBeOnPage( 'home' );
    }
}
