<?php

namespace EzSystems\BehatBundle\Context\BrowserSubContext;

use EzSystems\BehatBundle\Sentence\Authentication as AuthenticationSentences;
use Behat\Behat\Exception\PendingException;
use Behat\Behat\Context\Step;

class Authentication extends Base implements AuthenticationSentences
{
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

    public function iAmLoggedInAsWithPassword( $user, $password )
    {
        return array(
            new Step\Given( 'I am on "/login"' ),
            new Step\When( 'I fill in "Username" with "' . $user . '"' ),
            new Step\When( 'I fill in "Password" with "' . $password . '"' ),
            new Step\When( 'I press "Login"' ),
            new Step\Then( 'I should be on "/"' ),
        );
    }

    public function iAmNotLoggedIn()
    {
        return array(
            new Step\Given( 'I am on "/logout"' ),
            new Step\Then( 'I should be on "/"' ),
        );
    }
}
