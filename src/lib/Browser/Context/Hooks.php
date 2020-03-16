<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\Browser\Context;

use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\BeforeStepScope;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Testwork\Tester\Result\TestResult;
use EzSystems\Behat\Browser\Filter\BrowserLogFilter;
use EzSystems\Behat\Browser\Factory\ElementFactory;
use EzSystems\Behat\Browser\Factory\PageObjectFactory;
use EzSystems\Behat\Core\Environment\Environment;
use EzSystems\Behat\Core\Environment\EnvironmentConstants;
use EzSystems\Behat\Core\Log\LogFileReader;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use WebDriver\LogType;

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

    /** @BeforeScenario */
    public function logStartingScenario(BeforeScenarioScope $scope)
    {
        $this->logger->error(sprintf('Starting Scenario "%s"', $scope->getScenario()->getTitle()));
    }

    /** @BeforeStep */
    public function logStartingStep(BeforeStepScope $scope)
    {
        $this->logger->error(sprintf('Starting Step %s', $scope->getStep()->getText()));
    }

    /** @AfterScenario */
    public function logEndingScenario(AfterScenarioScope $scope)
    {
        $this->logger->error(sprintf('Ending Scenario "%s"', $scope->getScenario()->getTitle()));
    }

    /** @AfterStep */
    public function logEndingStep(AfterStepScope $scope)
    {
        $this->logger->error(sprintf('Ending Step %s', $scope->getStep()->getText()));
    }

    /** @AfterStep */
    public function getLogsAfterFailedStep(AfterStepScope $scope)
    {
        if ($scope->getTestResult()->getResultCode() !== TestResult::FAILED) {
            return;
        }

        $driver = $this->getSession()->getDriver();
        if ($driver instanceof Selenium2Driver) {
            $browserLogEntries = $this->parseBrowserLogs($driver->getWebDriverSession()->log(LogType::BROWSER));
            $this->displayLogEntries('JS console errors:', $browserLogEntries);
        }

        $logReader = new LogFileReader();
        $applicationLogEntries = $logReader->getLastLines(sprintf('%s/%s', $this->kernel->getLogDir(), self::LOG_FILE_NAME), self::APPLICATION_LOGS_LIMIT);

        $this->displayLogEntries('Application errors:', $applicationLogEntries);
    }

    private function parseBrowserLogs($logEntries): array
    {
        if (empty($logEntries)) {
            return [];
        }

        $filter = new BrowserLogFilter();
        $errorMessages = array_column($logEntries, 'message');
        $errorMessages = $filter->filter($errorMessages);

        return \array_slice($errorMessages, 0, self::CONSOLE_LOGS_LIMIT);
    }

    private function displayLogEntries($sectionName, $logEntries)
    {
        if (empty($logEntries)) {
            return;
        }

        echo sprintf('%s' . PHP_EOL, $sectionName);

        foreach ($logEntries as $logEntry) {
            echo sprintf('%s' . PHP_EOL, $logEntry);
        }
    }
}
