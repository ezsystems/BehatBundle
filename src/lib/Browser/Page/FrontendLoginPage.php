<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\Browser\Page;

class FrontendLoginPage extends Page
{
    /** @var string Name by which Page is recognised */
    public const PAGE_NAME = 'Frontend login page';

    public function verifyElements(): void
    {
    }

    public function logout(string $siteaccess): void
    {
        $this->context->visit(sprintf('/%s/%s', $siteaccess, 'logout'));
    }

    public function login($siteaccess, $username, $password)
    {
        $this->context->visit(sprintf('/%s/%s', $siteaccess, 'login'));
        $this->context->findElement('#username')->setValue($username);
        $this->context->findElement('#password')->setValue($password);
        $this->context->getElementByText('Login', 'button')->click();
    }
}
