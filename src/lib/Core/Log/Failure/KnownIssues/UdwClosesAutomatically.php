<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Core\Log\Failure\KnownIssues;

use Ibexa\Behat\Core\Log\Failure\TestFailureData;

class UdwClosesAutomatically implements KnownIssueInterface
{
    public function matches(TestFailureData $testFailureData): bool
    {
        return $testFailureData->exceptionStackTraceContainsFragment('UniversalDiscoveryWidget->selectTreeBranch()') &&
            $testFailureData->exceptionMessageContainsFragment("CSS selector 'treeLevelLocator':")
            && $testFailureData->browserLogsContainFragment("TypeError: Cannot read properties of undefined (reading 'isContainer')");
    }

    public function getJiraReference(): string
    {
        return 'https://issues.ibexa.co/browse/IBX-3076';
    }
}
