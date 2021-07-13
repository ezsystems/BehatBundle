<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Context;

use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\BeforeStepScope;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Testwork\Tester\Result\TestResult;
use EzSystems\Behat\Core\Log\TestLogProvider;
use Psr\Log\LoggerInterface;

class DebuggingContext extends RawMinkContext
{
    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var string */
    private $logDir;

    public function __construct(LoggerInterface $logger, string $logDir)
    {
        $this->logger = $logger;
        $this->logDir = $logDir;
    }

    /** @BeforeScenario
     */
    public function logStartingScenario(BeforeScenarioScope $scope)
    {
        $this->logger->error(sprintf('Behat: Starting Scenario "%s"', $scope->getScenario()->getTitle()));
    }

    /** @BeforeStep
     */
    public function logStartingStep(BeforeStepScope $scope)
    {
        $this->logger->error(sprintf('Behat: Starting Step "%s"', $scope->getStep()->getText()));
    }

    /** @AfterScenario
     */
    public function logEndingScenario(AfterScenarioScope $scope)
    {
        $this->logger->error(sprintf('Behat: Ending Scenario "%s"', $scope->getScenario()->getTitle()));
    }

    /** @AfterStep
     */
    public function logEndingStep(AfterStepScope $scope)
    {
        $this->logger->error(sprintf('Behat: Ending Step "%s"', $scope->getStep()->getText()));
    }

    /** @AfterStep */
    public function getLogsAfterFailedStep(AfterStepScope $scope)
    {
        if ($scope->getTestResult()->getResultCode() !== TestResult::FAILED) {
            return;
        }

        $testLogProvider = new TestLogProvider($this->getSession(), $this->logDir);

        $applicationsLogs = $testLogProvider->getApplicationLogs();
        $testEnvLogs = $testLogProvider->getBrowserLogs();

        echo $this->formatForDisplay($testEnvLogs, 'JS Console errors:');
        echo $this->formatForDisplay($applicationsLogs, 'Application logs:');
    }

    public function formatForDisplay(array $logEntries, string $sectionName)
    {
        $output = sprintf('%s' . PHP_EOL, $sectionName);

        if (empty($logEntries)) {
            $output .= sprintf("\t No logs for this section.") . PHP_EOL;
        }

        foreach ($logEntries as $logEntry) {
            $output .= sprintf("\t%s" . PHP_EOL, $logEntry);
        }

        return $output;
    }
}
