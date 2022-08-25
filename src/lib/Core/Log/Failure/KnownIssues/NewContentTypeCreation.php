<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Core\Log\Failure\KnownIssues;

use Ibexa\Behat\Core\Log\Failure\TestFailureData;

class NewContentTypeCreation implements KnownIssueInterface
{
    public function matches(TestFailureData $testFailureData): bool
    {
        return $testFailureData->exceptionStackTraceContainsFragment('ContentTypePicker->select()')
            && $testFailureData->exceptionMessageContainsFragment("Collection is empty. CSS locator 'filteredItem'");
    }

    public function getJiraReference(): string
    {
        return 'https://issues.ibexa.co/browse/IBX-3113';
    }
}
