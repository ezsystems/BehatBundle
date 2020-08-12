<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\Facade;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\TrashService;
use eZ\Publish\API\Repository\URLAliasService;
use eZ\Publish\API\Repository\Values\Content\URLAlias;
use PHPUnit\Framework\Assert;

class TrashFacade
{
    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\URLAliasService */
    private $urlAliasService;

    /** @var eZ\Publish\API\Repository\TrashService */
    private $trashService;

    public function __construct(LocationService $locationService, URLAliasService $urlAliasService, TrashService $trashService)
    {
        $this->locationService = $locationService;
        $this->urlAliasService = $urlAliasService;
        $this->trashService = $trashService;
    }

    public function trash(string $locationURL)
    {
        $urlAlias = $this->urlAliasService->lookup($locationURL);
        Assert::assertEquals(URLAlias::LOCATION, $urlAlias->type);

        $location = $this->locationService->loadLocation($urlAlias->destination);

        $this->trashService->trash($location);
    }
}
