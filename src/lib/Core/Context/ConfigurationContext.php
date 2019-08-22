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
    private const SITEACCESS_KEY_FORMAT = 'ezpublish.system.%s.%s';

    private $ezplatformConfigFilePath;

    public function __construct(string $projectDir)
    {
        $this->ezplatformConfigFilePath = sprintf('%s/config/packages/ezplatform.yaml', $projectDir);
    }

    /**
     * @Given I add a siteaccess :siteaccessName to :siteaccessGroup with settings
     */
    public function iAddSiteaccessWithSettings($siteaccessName, $siteaccessGroup, TableNode $settings)
    {
        $configurationEditor = new ConfigurationEditor();

        $config = $configurationEditor->getConfigFromFile($this->ezplatformConfigFilePath);

        $config = $configurationEditor->append($config, 'ezpublish.siteaccess.list', $siteaccessName);
        $config = $configurationEditor->append($config, sprintf('ezpublish.siteaccess.groups.%s', $siteaccessGroup), $siteaccessName);

        foreach ($settings->getHash() as $setting) {
            $key = $setting['key'];
            $value = $this->parseSetting($setting['value']);
            $config = $configurationEditor->set($config, sprintf(self::SITEACCESS_KEY_FORMAT, $siteaccessName, $key), $value);
        }

        $configurationEditor->saveConfigToFile($this->ezplatformConfigFilePath, $config);
    }

    /**
     * @Given I append configuration to :siteaccessName siteaccess
     */
    public function iAppendConfigurationToSiteaccess($siteaccessName, TableNode $settings)
    {
        $configurationEditor = new ConfigurationEditor();
        $config = $configurationEditor->getConfigFromFile($this->ezplatformConfigFilePath);

        foreach ($settings->getHash() as $setting) {
            $key = $setting['key'];
            $value = $this->parseSetting($setting['value']);
            $config = $configurationEditor->append($config, sprintf(self::SITEACCESS_KEY_FORMAT, $siteaccessName, $key), $value);
        }

        $configurationEditor->saveConfigToFile($this->ezplatformConfigFilePath, $config);
    }

    /**
     * @Given I :mode configuration to :siteaccessName siteaccess under :keyName key
     */
    public function iModifyConfigurationForSiteaccessUnderKey(string $mode, $siteaccessName, $keyName, PyStringNode $configFragment)
    {
        $appendToExisting = $this->shouldAppendValue($mode);

        $configurationEditor = new ConfigurationEditor();

        $config = $configurationEditor->getConfigFromFile($this->ezplatformConfigFilePath);
        $parsedConfig = $this->parseConfig($configFragment);
        $parentNode = sprintf(self::SITEACCESS_KEY_FORMAT, $siteaccessName, $keyName);

        $config = $appendToExisting ?
            $configurationEditor->append($config, $parentNode, $parsedConfig) :
            $configurationEditor->set($config, $parentNode, $parsedConfig);
        $configurationEditor->saveConfigToFile($this->ezplatformConfigFilePath, $config);
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
