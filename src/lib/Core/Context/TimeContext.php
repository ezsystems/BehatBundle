<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\Core\Context;

use Behat\Behat\Context\Context;

class TimeContext implements Context
{
    /**
     * @Given I wait :number seconds
     */
    public function iWait($number): void
    {
        sleep($number);
    }
}
