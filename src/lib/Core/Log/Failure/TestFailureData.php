<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Core\Log\Failure;

use Behat\Testwork\Tester\Result\ExceptionResult;

class TestFailureData
{
    /** @var \Behat\Testwork\Tester\Result\ExceptionResult */
    private $failedStepResult;

    /** @var string[] */
    private $applicationLogs;

    /** @var string[] */
    private $browserLogs;

    public function __construct(ExceptionResult $failedStepResult, array $applicationLogs, array $browserLogs)
    {
        $this->failedStepResult = $failedStepResult;
        $this->applicationLogs = $applicationLogs;
        $this->browserLogs = $browserLogs;
    }

    public function getFailedStepsResult(): ExceptionResult
    {
        return $this->failedStepResult;
    }

    /**
     * @return string[]
     */
    public function getApplicationLogs(): array
    {
        return $this->applicationLogs;
    }

    /**
     * @return string[]
     */
    public function getBrowserLogs(): array
    {
        return $this->browserLogs;
    }

    public function applicationLogContainsFragment($logFragment)
    {
        foreach ($this->getApplicationLogs() as $logEntry) {
            if (strpos($logEntry, $logFragment) !== false) {
                return true;
            }
        }

        return false;
    }

    public function exceptionStackTraceContainsFragment(string $stackTraceFragment)
    {
        return strpos($this->getFailedStepsResult()->getException()->getTraceAsString(), $stackTraceFragment) !== false;
    }

    public function exceptionMessageContainsFragment(string $exceptionMessageFragment)
    {
        $exceptionMessage = $this->getFailedStepsResult()->getException()->getMessage();

        return strpos($exceptionMessage, $exceptionMessageFragment) !== false;
    }

    public function browserLogsContainFragment(string $logFragment)
    {
        foreach ($this->getBrowserLogs() as $browserLogEntry) {
            if (strpos($browserLogEntry, $logFragment) !== false) {
                return true;
            }
        }

        return false;
    }
}
