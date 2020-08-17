<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\Context;

use Behat\Behat\Context\Context;
use EzSystems\Behat\API\Facade\TrashFacade;
use EzSystems\Behat\Core\Behat\ArgumentParser;

class TrashContext implements Context
{
    /** @var TrashFacade */
    private $trashFacade;

    /** @var ArgumentParser */
    private $argumentParser;

    public function __construct(TrashFacade $trashFacade, ArgumentParser $argumentParser)
    {
        $this->trashFacade = $trashFacade;
        $this->argumentParser = $argumentParser;
    }

    /**
     * @Then I send :locationURL to the Trash
     */
    public function iSendToTheTrash($locationURL)
    {
        $locationURL = $this->argumentParser->parseUrl($locationURL);
        $this->trashFacade->trash($locationURL);
    }
}
