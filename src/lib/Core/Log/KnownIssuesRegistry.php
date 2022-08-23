<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Core\Log;

use Ibexa\Behat\Core\Log\Failure\AnalysisResult;
use Ibexa\Behat\Core\Log\Failure\TestFailureData;

final class KnownIssuesRegistry
{
    /** @var \Ibexa\Behat\Core\Log\Failure\KnownIssues\KnownIssueInterface[] */
    private $knownIssues;

    public function __construct(iterable $knownIssues)
    {
        $this->knownIssues = $knownIssues;
    }

    public function isKnown(TestFailureData $failureData): AnalysisResult
    {
        foreach ($this->knownIssues as $knownIssue) {
            if ($knownIssue->matches($failureData)) {
                return new AnalysisResult(true, $knownIssue->getJiraReference());
            }
        }

        return new AnalysisResult(false);
    }
}
