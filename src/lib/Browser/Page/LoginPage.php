<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Page;

use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Element\Criterion\LogicalOrCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
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
        $this->getHTMLPage()->findAll($this->getLocator('button'))
            ->getByCriterion(new LogicalOrCriterion([
                new ElementTextCriterion('Login'),
                new ElementTextCriterion('Sign in'),
            ]))
            ->click();

        Assert::assertNotEquals('/login', $this->getSession()->getCurrentUrl());
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertStringContainsString($this->getRoute(), $this->getSession()->getCurrentUrl());
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
            new VisibleCSSLocator('username', '#username'),
            new VisibleCSSLocator('password', '#password'),
            new VisibleCSSLocator('button', 'button[type=submit]'),
        ];
    }
}
