<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Routing;

use eZ\Publish\Core\MVC\Symfony\Routing\SimplifiedRequest;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\Router as CoreRouter;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;

final class Router
{
    /** @var \eZ\Publish\Core\MVC\Symfony\SiteAccess\Router */
    private $router;

    /** @var \FriendsOfBehat\SymfonyExtension\Mink\MinkParameters */
    private $minkParameters;

    public function __construct(CoreRouter $router, MinkParameters $minkParameters)
    {
        $this->router = $router;
        $this->minkParameters = $minkParameters;
    }

    public function reverseMatchRoute(string $siteAccessName, string $route): string
    {
        $matcher = $this->router->matchByName($siteAccessName)->matcher;
        $matcher->setRequest(new SimplifiedRequest(['scheme' => 'http', 'host' => $this->minkParameters['base_url'], 'pathinfo' => $route]));
        $request = $matcher->reverseMatch($siteAccessName)->getRequest();

        $explodedHost = explode('//', $request->host);
        $actualHost = $explodedHost[count($explodedHost) - 1];

        return sprintf('%s://%s%s', $request->scheme, $actualHost, $request->pathinfo);
    }
}
