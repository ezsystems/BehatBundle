<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Page;

use Behat\Mink\Session;
use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Routing\Router;

abstract class Page extends Component implements PageInterface
{
    /** @var \Ibexa\Behat\Browser\Routing\Router */
    private $router;

    public function __construct(Session $session, Router $router)
    {
        parent::__construct($session);
        $this->router = $router;
    }

    abstract public function getName(): string;

    public function open(string $siteaccess): void
    {
        $this->tryToOpen($siteaccess);
        $this->verifyIsLoaded();
    }

    public function tryToOpen(string $siteaccess): void
    {
        $url = $this->router->reverseMatchRoute($siteaccess, $this->getRoute());

        if (!$this->getSession()->isStarted()) {
            $this->getSession()->start();
        }
        $this->getSession()->visit($url);
    }

    abstract protected function getRoute(): string;
}
