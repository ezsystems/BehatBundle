<?php
/**
 * File containing the Exception sentences interface for BehatBundle.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Sentence;

interface Exception
{
    /**
     * @Then /^I see (?:an |)invalid field error$/
     */
    public function iSeeAnInvalidFieldError();

    /**
     * @Then /^I see (?:a |)not authorized error$/
     * @Then /^I see (?:an |)unauthorized error$/
     */
    public function iSeeNotAuthorizedError();
}
