<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Context;

use Behat\MinkExtension\Context\RawMinkContext;
use EzSystems\Behat\API\ContentData\FieldTypeData\PasswordProvider;
use Ibexa\Behat\Browser\Page\LoginPage;
use Ibexa\Behat\Browser\Page\RedirectLoginPage;

class AuthenticationContext extends RawMinkContext
{
    /**
     * @var \Ibexa\Behat\Browser\Page\LoginPage
     */
    private $loginPage;
    /**
     * @var \Ibexa\Behat\Browser\Page\RedirectLoginPage
     */
    private $redirectLoginPage;

    public function __construct(LoginPage $loginPage, RedirectLoginPage $redirectLoginPage)
    {
        $this->loginPage = $loginPage;
        $this->redirectLoginPage = $redirectLoginPage;
    }

    /**
     * @Given I log in as :username
     * @Given I log in as :username with password :password
     */
    public function iLogInIn(string $username, string $password = null)
    {
        $password = $password ?? PasswordProvider::DEFAUlT_PASSWORD;
        $this->loginPage->loginSuccessfully($username, $password);
    }

    /**
     * @Given I am logged as admin
     */
    public function loggedAsAdmin()
    {
        $this->redirectLoginPage->open('admin');
        $this->redirectLoginPage->loginSuccessfully('admin', 'publish');
    }

    /**
     * @Given I am viewing the pages on siteaccess :siteaccess as :username
     * @Given I am viewing the pages on siteaccess :siteaccess as :username :password
     * @Given I am viewing the pages on siteaccess :siteaccess as :username with password :password
     */
    public function iAmViewingThePagesAsUserOnSiteaccess(string $siteaccess, string $username, string $password = null)
    {
        $this->loginPage->open($siteaccess);
        $this->loginPage->logout($siteaccess);

        if (!$this->shouldPerformLoginAction($username)) {
            return;
        }

        $password = $password ?? PasswordProvider::DEFAUlT_PASSWORD;
        $this->loginPage->open($siteaccess);
        $this->loginPage->loginSuccessfully($username, $password);
    }

    private function shouldPerformLoginAction(string $username)
    {
        return 'anonymous' !== strtolower($username);
    }
}
