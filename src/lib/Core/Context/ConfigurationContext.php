<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\Core\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Exception;
use EzSystems\Behat\Core\Configuration\ConfigurationEditor;
use Symfony\Component\Yaml\Yaml;

class ConfigurationContext implements Context
{
    private const SITEACCESS_KEY_FORMAT = 'ezplatform.system.%s.%s';
    private const SITEACCESS_MATCHER_KEY = 'ezplatform.siteaccess.match';
    private $ezplatformConfigFilePath;
    private $configFilePath;
    private $projectDir;

    /**
     * @injectService $projectDir %kernel.project_dir%
     */
    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
        $this->ezplatformConfigFilePath = sprintf('%s/config/packages/ezplatform.yaml', $projectDir);
    }

    /**
     * @Given I add a siteaccess :siteaccessName to :siteaccessGroup with settings
     */
    public function iAddSiteaccessWithSettings($siteaccessName, $siteaccessGroup, TableNode $settings)
    {
        $configurationEditor = new ConfigurationEditor();

        $config = $configurationEditor->getConfigFromFile($this->ezplatformConfigFilePath);

        $config = $configurationEditor->append($config, 'ezplatform.siteaccess.list', $siteaccessName);
        $config = $configurationEditor->append($config, sprintf('ezplatform.siteaccess.groups.%s', $siteaccessGroup), $siteaccessName);

        foreach ($settings->getHash() as $setting) {
            $key = $setting['key'];
            $value = $this->parseSetting($setting['value']);
            $config = $configurationEditor->set($config, sprintf(self::SITEACCESS_KEY_FORMAT, $siteaccessName, $key), $value);
        }

        $configurationEditor->saveConfigToFile($this->ezplatformConfigFilePath, $config);
    }

    /**
     * @Given I :mode configuration to :siteaccessName siteaccess
     */
    public function iAppendOrSetConfigurationToSiteaccess(string $mode, $siteaccessName, TableNode $settings)
    {
        $appendToExisting = $this->shouldAppendValue($mode);

        $configurationEditor = new ConfigurationEditor();
        $config = $configurationEditor->getConfigFromFile($this->ezplatformConfigFilePath);

        foreach ($settings->getHash() as $setting) {
            $key = $setting['key'];
            $value = $this->parseSetting($setting['value']);

            $config = $appendToExisting ?
                $configurationEditor->append($config, sprintf(self::SITEACCESS_KEY_FORMAT, $siteaccessName, $key), $value) :
                $configurationEditor->set($config, sprintf(self::SITEACCESS_KEY_FORMAT, $siteaccessName, $key), $value);
        }

        $configurationEditor->saveConfigToFile($this->ezplatformConfigFilePath, $config);
    }

    /**
     * @Given I :mode configuration to :parentNode
     * @Given I :mode configuration to :parentNode in :configFilePath
     *
     * string $mode Available: append|set - whether the new config will be appended (resulting in an array) or replace the current value if it exists
     */
    public function iModifyConfigurationUnderKey(string $mode, $parentNode, PyStringNode $configFragment, string $configFilePath = null)
    {
        $appendToExisting = $this->shouldAppendValue($mode);

        $configurationEditor = new ConfigurationEditor();

        $configFilePath = $configFilePath ? sprintf('%s/%s', $this->projectDir, $configFilePath) : $this->ezplatformConfigFilePath;

        $config = $configurationEditor->getConfigFromFile($configFilePath);
        $parsedConfig = $this->parseConfig($configFragment);

        $config = $appendToExisting ?
            $configurationEditor->append($config, $parentNode, $parsedConfig) :
            $configurationEditor->set($config, $parentNode, $parsedConfig);
        $configurationEditor->saveConfigToFile($configFilePath, $config);
    }

    /**
     * @Given I :mode configuration to :siteaccessName siteaccess under :keyName key
     */
    public function iModifyConfigurationForSiteaccessUnderKey(string $mode, $siteaccessName, $keyName, PyStringNode $configFragment)
    {
        $parentNode = sprintf(self::SITEACCESS_KEY_FORMAT, $siteaccessName, $keyName);
        $this->iModifyConfigurationUnderKey($mode, $parentNode, $configFragment);
    }

    /**
     * @Given I :mode siteaccess matcher configuration
     */
    public function iModifySiteaccessMatcherConfiguration(string $mode, PyStringNode $configFragment)
    {
        $this->iModifyConfigurationUnderKey($mode, self::SITEACCESS_MATCHER_KEY, $configFragment);
    }

    private function parseSetting($setting)
    {
        return strpos($setting, ',') !== false ? explode(',', $setting) : $setting;
    }

    private function parseConfig(PyStringNode $configFragment)
    {
        $cleanedConfig = '';

        // Remove indent from first line and adjust the rest
        $firstLine = $configFragment->getStrings()[0];
        $firstLineIndent = \strlen($firstLine) - \strlen(ltrim($firstLine));

        foreach ($configFragment->getStrings() as $line) {
            $cleanedConfig = $cleanedConfig . substr($line, $firstLineIndent) . PHP_EOL;
        }

        return Yaml::parse($cleanedConfig);
    }

    private function shouldAppendValue(string $value): bool
    {
        if (!\in_array($value, ['set', 'append'])) {
            throw new Exception('Supported modes are: set, append');
        }

        return 'append' === $value;
    }
}
