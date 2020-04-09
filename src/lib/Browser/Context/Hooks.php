<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Browser\Context;

use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\BeforeStepScope;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Testwork\Tester\Result\TestResult;
use EzSystems\Behat\Browser\Factory\ElementFactory;
use EzSystems\Behat\Browser\Factory\PageObjectFactory;
use EzSystems\Behat\Core\Environment\Environment;
use EzSystems\Behat\Core\Environment\EnvironmentConstants;
use EzSystems\Behat\Core\Log\TestLogProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class Hooks extends RawMinkContext
{
    private const CONSOLE_LOGS_LIMIT = 10;
    private const APPLICATION_LOGS_LIMIT = 25;
    private const LOG_FILE_NAME = 'travis_test.log';

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \Symfony\Component\HttpKernel\KernelInterface */
    private $kernel;

    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    private $container;

    public function __construct(LoggerInterface $logger, KernelInterface $kernel, ContainerInterface $container)
    {
        $this->logger = $logger;
        $this->kernel = $kernel;
        $this->container = $container;
    }

    /** @BeforeScenario */
    public function setInstallTypeBeforeScenario()
    {
        $container = $this->container->has('test.service_container')
            ? $this->container->get('test.service_container')
            : $this->container;

        $env = new Environment($container);
        $installType = $env->getInstallType();

        PageObjectFactory::setInstallType($installType);
        ElementFactory::setInstallType($installType);
        EnvironmentConstants::setInstallType($installType);
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

        $testLogProvider = new TestLogProvider($this->getSession(), $this->kernel->getLogDir());

        $applicationsLogs = $testLogProvider->getApplicationLogs();
        $browserLogs = $testLogProvider->getBrowserLogs();

        echo $this->formatForDisplay($browserLogs, 'JS Console errors:');
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
