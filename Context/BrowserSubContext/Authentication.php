<?php
/**
 * File containing the Authentication class for Browser contexts.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Context\BrowserSubContext;

use EzSystems\BehatBundle\Sentence\Authentication as AuthenticationSentences;
use Behat\Behat\Exception\PendingException;
use Behat\Behat\Context\Step;

class Authentication extends Base implements AuthenticationSentences
{
    /**
     * Given I am not logged in
     * Given I do not|don't have permissions
     */
    public function iAmLoggedInAsAn( $role )
    {
        switch( strtolower( $role ) ) {
        case 'administrator':
            $user = 'admin';
            $password = 'publish';
            break;

        default:
            throw new PendingException( "Login with '$role' role not implemented yet" );
        }

        return $this->iAmLoggedInAsWithPassword( $user, $password );
    }

    /**
     * Given I am logged in as "<user>" with password "<password>"
     */
    public function iAmLoggedInAsWithPassword( $user, $password )
    {
        return array(
            new Step\Given( 'I am on "login" page' ),
            new Step\When( 'I fill in "Username" with "' . $user . '"' ),
            new Step\When( 'I fill in "Password" with "' . $password . '"' ),
            new Step\When( 'I press "Login"' ),
            new Step\Then( 'I should be on "/"' ),
        );
    }

    /**
     * Given I am logged in as an|a "<role>"
     * Given I have "<role>" permissions
     */
    public function iAmNotLoggedIn()
    {
        return array(
            new Step\Given( 'I am on "logout" page' ),
            new Step\Then( 'I should be on "/"' ),
        );
    }
}
