<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\Context\Object;

use Behat\Behat\Context\Context;
use EzSystems\BehatBundle\API\Facade\TrashFacade;
use EzSystems\BehatBundle\Helper\ArgumentParser;

class TrashContext implements Context
{
    /** @var TrashFacade */
    private $trashFacade;

    /** @var ArgumentParser */
    private $argumentParser;

    /**
     * @injectService $trashFacade @EzSystems\BehatBundle\API\Facade\TrashFacade
     * @injectService $argumentParser @EzSystems\BehatBundle\Helper\ArgumentParser
     */
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
