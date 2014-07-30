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
    const REGEX_NOT_FOUND_MESSAGE       = "/^Could not find '([^']+)' with identifier '([^']+)'$/";
    const REGEX_INVALID_FIELD_MESSAGE   = "/^Argument '([^']+)' is invalid:(.+)$/";

    /**
     * @Then /^I see (?:an |)invalid field (?:exception|error)$/
     */
    public function iSeeAnInvalidFieldError();

    /**
     * @Then /^I see (?:a |)forbidden (?:exception|error)$/
     */
    public function iSeeAForbiddenError();

    /**
     * @Then /^I see (?:a |)forbidden (?:exception|error) with "(?P<message>[^"]+)" message$/
     */
    public function iSeeAForbiddenErrorWithMessage( $message );

    /**
     * @Then /^I see (?:a |)not authorized (?:exception|error)$/
     * @Then /^I see (?:an |)unauthorized (?:exception|error)$/
     */
    public function iSeeNotAuthorizedError();

    /**
     * @Then /^I see (?:a |)not found (?:exception|error)$/
     */
    public function iSeeNotFoundError();
}
