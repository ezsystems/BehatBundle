<?php
/**
 * File containing the Authentication class for Browser contexts.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Context\BrowserSubContexts;

trait Authentication
{
    /**
     * @Given I am logged (in) as a(n) :role
     * @Given I have :role permissions
     */
    public function iAmLoggedInAsAn( $role )
    {
        $credentials = $this->getCredentialsFor( 'administrator' );
        $this->iAmLoggedInAsWithPassword( $credentials['login'], $credentials['password'] );
    }

    /**
     * @Given I am logged in as :user with password :password
     */
    public function iAmLoggedInAsWithPassword( $user, $password )
    {
        $this->iAmOnPage( 'login' );
        $this->fillField( 'Username', $user );
        $this->fillField( 'Password', $password );
        $this->iClickAtButton( 'Login' );
        $this->iShouldBeOnPage( 'home' );
    }

    /**
     * @Given I am not logged in
     * @Given I don't have permissions
     */
    public function iAmNotLoggedIn()
    {
        $this->iAmOnPage( 'logout' );
        $this->iShouldBeOnPage( 'home' );
    }
}
