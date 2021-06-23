<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Page;

use Ibexa\Behat\Browser\Locator\CSSLocator;
use PHPUnit\Framework\Assert;

class LoginPage extends Page
{
    public function logout(): void
    {
        $currentUrl = $this->getSession()->getCurrentUrl();
        $logoutUrl = str_replace('/login', '/logout', $currentUrl);
        $this->getSession()->visit($logoutUrl);
    }

    public function loginSuccessfully($username, $password): void
    {
        $this->getHTMLPage()->find($this->getLocator('username'))->setValue($username);
        $this->getHTMLPage()->find($this->getLocator('password'))->setValue($password);
        $this->getHTMLPage()->find($this->getLocator('button'))->click();

        Assert::assertNotEquals('/login', $this->getSession()->getCurrentUrl());
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertContains($this->getRoute(), $this->getSession()->getCurrentUrl());
    }

    public function getName(): string
    {
        return 'Login';
    }

    protected function getRoute(): string
    {
        return '/login';
    }

    protected function specifyLocators(): array
    {
        return [
            new CSSLocator('username', '#username'),
            new CSSLocator('password', '#password'),
            new CSSLocator('button', 'button[type=submit]'),
        ];
    }
}
