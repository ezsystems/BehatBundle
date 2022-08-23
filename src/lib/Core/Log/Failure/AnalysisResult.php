<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Core\Log\Failure;

final class AnalysisResult
{
    /** @var bool */
    private $isKnownFailure;

    /** @var string|null */
    private $jiraReference;

    public function __construct(bool $isKnownFailure, string $jiraReference = '')
    {
        $this->isKnownFailure = $isKnownFailure;
        $this->jiraReference = $jiraReference;
    }

    public function isKnownFailure(): bool
    {
        return $this->isKnownFailure;
    }

    public function getJiraReference(): string
    {
        return $this->jiraReference;
    }
}
