<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\Facade;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\TrashService;
use Ibexa\Contracts\Core\Repository\URLAliasService;
use Ibexa\Contracts\Core\Repository\Values\Content\URLAlias;
use PHPUnit\Framework\Assert;

class TrashFacade
{
    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\URLAliasService */
    private $urlAliasService;

    /** @var \Ibexa\Contracts\Core\Repository\TrashService */
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
