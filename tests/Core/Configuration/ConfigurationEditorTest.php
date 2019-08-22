<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\Behat\Test\Core\Configuration;

use EzSystems\Behat\Core\Configuration\ConfigurationEditor;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ConfigurationEditorTest extends TestCase
{
    public function testAppendsValueToConfigWhenKeyDoesNotExistAndConfigIsEmpty()
    {
        $configurationEditor = new ConfigurationEditor();
        $initialConfig = [];

        $config = $configurationEditor->append($initialConfig, 'testKey', 'testValue');

        Assert::assertEquals(['testKey' => ['testValue']], $config);
    }

    public function testAppendsValueToConfigWhenKeyDoesNotExistAndConfigIsNotEmpty()
    {
        $configurationEditor = new ConfigurationEditor();
        $initialConfig = ['initialKey' => 'initialValue'];

        $config = $configurationEditor->append($initialConfig, 'testKey', 'testValue');

        Assert::assertEquals(['initialKey' => 'initialValue', 'testKey' => ['testValue']], $config);
    }

    public function testAppendsValueToConfigWhenCurrentValueIsString()
    {
        $configurationEditor = new ConfigurationEditor();
        $initialConfig = ['testKey' => 'initialValue'];

        $config = $configurationEditor->append($initialConfig, 'testKey', 'testValue');

        Assert::assertEquals(['testKey' => ['initialValue', 'testValue']], $config);
    }

    public function testAppendsValueToConfigWhenCurrentValueIsArray()
    {
        $configurationEditor = new ConfigurationEditor();
        $initialConfig = ['testKey' => ['initialValue1', 'initialValue2']];

        $config = $configurationEditor->append($initialConfig, 'testKey', 'testValue');

        Assert::assertEquals(['testKey' => ['initialValue1', 'initialValue2', 'testValue']], $config);
    }

    public function testAppendsArrayValueToConfigWhenCurrentValueIsString()
    {
        $configurationEditor = new ConfigurationEditor();
        $initialConfig = ['testKey' => 'initialValue'];

        $config = $configurationEditor->append($initialConfig, 'testKey', ['testValue1', 'testValue2']);

        Assert::assertEquals(['testKey' => ['initialValue', 'testValue1', 'testValue2']], $config);
    }

    public function testAppendsArrayValueToConfigWhenCurrentValueIsArray()
    {
        $configurationEditor = new ConfigurationEditor();
        $initialConfig = ['testKey' => ['initialValue1', 'initialValue2']];

        $config = $configurationEditor->append($initialConfig, 'testKey', ['testValue1', 'testValue2']);

        Assert::assertEquals(['testKey' => ['initialValue1', 'initialValue2', 'testValue1', 'testValue2']], $config);
    }

    public function testAppendsValueToConfigWhenKeyIsNested()
    {
        $configurationEditor = new ConfigurationEditor();
        $initialConfig = ['testKey' => ['initialValue']];

        $config = $configurationEditor->append($initialConfig, 'testKey.testSection', 'testValue1');

        Assert::assertEquals(['testKey' => ['initialValue', 'testSection' => ['testValue1']]], $config);
    }

    public function testAppendsArrayValueToConfigWhenKeyIsNested()
    {
        $configurationEditor = new ConfigurationEditor();
        $initialConfig = ['testKey' => ['initialValue']];

        $config = $configurationEditor->append($initialConfig, 'testKey.testSection', ['testValue1', 'testValue2']);

        Assert::assertEquals(['testKey' => ['initialValue', 'testSection' => ['testValue1','testValue2']]], $config);
    }

    public function testCanAddAnotherValueWithConfigurationEditor()
    {
        $configurationEditor = new ConfigurationEditor();
        $initialConfig = [];

        $config = $configurationEditor->append($initialConfig, 'testKey', 'testValue1');
        $config = $configurationEditor->append($config, 'testKey', 'testValue2');

        Assert::assertEquals(['testKey' => ['testValue1','testValue2']], $config);
    }

    public function testCanAddNestedValueWithConfigurationEditor()
    {
        $configurationEditor = new ConfigurationEditor();
        $initialConfig = [];

        $config = $configurationEditor->append($initialConfig, 'testKey', 'testValue1');
        $config = $configurationEditor->append($config, 'testKey.nested', 'testValue2');

        Assert::assertEquals(['testKey' => ['testValue1', 'nested' => ['testValue2']]], $config);
    }

    public function testCanAddToNestedValue()
    {
        $configurationEditor = new ConfigurationEditor();
        $initialConfig = [];

        $config = $configurationEditor->append($initialConfig, 'testKey.nested', 'testValue2');
        $config = $configurationEditor->append($config, 'testKey', 'testValue1');

        Assert::assertEquals(['testKey' => ['testValue1', 'nested' => ['testValue2']]], $config);
    }

    public function testSetsValueToConfigWhenKeyDoesNotExistAndConfigIsEmpty()
    {
        $configurationEditor = new ConfigurationEditor();
        $initialConfig = [];

        $config = $configurationEditor->set($initialConfig, 'testKey', ['testValue']);

        Assert::assertEquals(['testKey' => ['testValue']], $config);
    }

    public function testSetsValueToConfigWhenKeyDoesNotExistAndConfigIsNotEmpty()
    {
        $configurationEditor = new ConfigurationEditor();
        $initialConfig = ['initialKey' => 'initialValue'];

        $config = $configurationEditor->set($initialConfig, 'testKey', ['testValue']);

        Assert::assertEquals(['initialKey' => 'initialValue', 'testKey' => ['testValue']], $config);
    }

    public function testSetsValueToConfigWhenCurrentValueIsString()
    {
        $configurationEditor = new ConfigurationEditor();
        $initialConfig = ['testKey' => 'initialValue'];

        $config = $configurationEditor->set($initialConfig, 'testKey', ['testValue']);

        Assert::assertEquals(['testKey' => ['testValue']], $config);
    }

    public function testSetsValueToConfigWhenCurrentValueIsArray()
    {
        $configurationEditor = new ConfigurationEditor();
        $initialConfig = ['testKey' => ['initialValue1', 'initialValue2']];

        $config = $configurationEditor->set($initialConfig, 'testKey','testValue');

        Assert::assertEquals(['testKey' => 'testValue'], $config);
    }

    public function testSetsArrayValueToConfigWhenCurrentValueIsString()
    {
        $configurationEditor = new ConfigurationEditor();
        $initialConfig = ['testKey' => 'initialValue'];

        $config = $configurationEditor->set($initialConfig, 'testKey', ['testValue1', 'testValue2']);

        Assert::assertEquals(['testKey' => ['testValue1', 'testValue2']], $config);
    }

    public function testSetsArrayValueToConfigWhenCurrentValueIsArray()
    {
        $configurationEditor = new ConfigurationEditor();
        $initialConfig = ['testKey' => ['initialValue1', 'initialValue2']];

        $config = $configurationEditor->set($initialConfig, 'testKey', ['testValue1', 'testValue2']);

        Assert::assertEquals(['testKey' => ['testValue1', 'testValue2']], $config);
    }

    public function testSetsValueToConfigWhenKeyIsNested()
    {
        $configurationEditor = new ConfigurationEditor();
        $initialConfig = ['testKey' => ['initialValue']];

        $config = $configurationEditor->set($initialConfig, 'testKey.testSection', 'testValue1');

        Assert::assertEquals(['testKey' => ['initialValue', 'testSection' => 'testValue1']], $config);
    }

    public function testSetsArrayValueToConfigWhenKeyIsNested()
    {
        $configurationEditor = new ConfigurationEditor();
        $initialConfig = ['testKey' => ['initialValue']];

        $config = $configurationEditor->set($initialConfig, 'testKey.testSection', ['testValue1', 'testValue2']);

        Assert::assertEquals(['testKey' => ['initialValue', 'testSection' => ['testValue1','testValue2']]], $config);
    }

    public function testCanSetAnotherValueWithConfigurationEditor()
    {
        $configurationEditor = new ConfigurationEditor();
        $initialConfig = [];

        $config = $configurationEditor->set($initialConfig, 'testKey', 'testValue1');
        $config = $configurationEditor->set($config, 'testKey', 'testValue2');

        Assert::assertEquals(['testKey' => 'testValue2'], $config);
    }

    public function testCanSetNestedValueWithConfigurationEditor()
    {
        $configurationEditor = new ConfigurationEditor();
        $initialConfig = [];

        $config = $configurationEditor->set($initialConfig, 'testKey', ['testValue1']);
        $config = $configurationEditor->set($config, 'testKey.nested', 'testValue2');

        Assert::assertEquals(['testKey' => ['testValue1', 'nested' => 'testValue2']], $config);
    }

    public function testCanRemoveNestedValueWithConfigurationEditor()
    {
        $configurationEditor = new ConfigurationEditor();
        $initialConfig = [];

        $config = $configurationEditor->set($initialConfig, 'testKey.nested', 'testValue2');
        $config = $configurationEditor->set($config, 'testKey', 'testValue1');

        Assert::assertEquals(['testKey' => 'testValue1'], $config);
    }

    public function testSetStringValueWithConfigurationEditor()
    {
        $configurationEditor = new ConfigurationEditor();
        $initialConfig = [];

        $config = $configurationEditor->set($initialConfig, 'testKey.nested', 'testValue2');

        Assert::assertEquals(['testKey' => ['nested' => 'testValue2']], $config);
    }

    public function testSetIntegerValueWithConfigurationEditor()
    {
        $configurationEditor = new ConfigurationEditor();
        $initialConfig = [];

        $config = $configurationEditor->set($initialConfig, 'testKey.nested', 1);

        Assert::assertEquals(['testKey' => ['nested' => 1]], $config);
    }
}