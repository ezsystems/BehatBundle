<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Test\Core\Configuration;

use EzSystems\Behat\Core\Configuration\ConfigurationEditor;
use EzSystems\Behat\Core\Configuration\ProjectEditionAwareConfigurationEditor;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ProjectEditionAwareConfigurationEditorTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            [$this->createEditor(__DIR__ . '/Data/' . 'oss'), 'oss'],
            [$this->createEditor(__DIR__ . '/Data/' . 'content'), 'content'],
            [$this->createEditor(__DIR__ . '/Data/' . 'experience'), 'experience'],
            [$this->createEditor(__DIR__ . '/Data/' . 'commerce'), 'commerce'],
        ];
    }

    private function createEditor(string $projectDir): ProjectEditionAwareConfigurationEditor
    {
        return new ProjectEditionAwareConfigurationEditor(new ConfigurationEditor(), $projectDir);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testDoesNotFailOnNonStrings(
        ProjectEditionAwareConfigurationEditor $projectEditionAwareConfigurationEditor,
        string $expectedResult): void
    {
        $initialConfig = ['testKey' => [1, null]];

        $value = $projectEditionAwareConfigurationEditor->get($initialConfig, 'testKey');

        Assert::assertEquals([1, null], $value);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testReplacesSingleValue(
        ProjectEditionAwareConfigurationEditor $projectEditionAwareConfigurationEditor,
        string $expectedResult): void
    {
        $initialConfig = ['testKey' => '%project_edition%'];

        $value = $projectEditionAwareConfigurationEditor->get($initialConfig, 'testKey');

        Assert::assertEquals($expectedResult, $value);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testReplacesMultipleValues(
        ProjectEditionAwareConfigurationEditor $projectEditionAwareConfigurationEditor,
        string $expectedResult): void
    {
        $initialConfig = ['testKey' => ['%project_edition%', '%project_edition%']];

        $value = $projectEditionAwareConfigurationEditor->get($initialConfig, 'testKey');

        Assert::assertEquals([$expectedResult, $expectedResult], $value);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testReplacesValueWhenSetIsUsed(
        ProjectEditionAwareConfigurationEditor $projectEditionAwareConfigurationEditor,
        string $expectedResult): void
    {
        $initialConfig = ['testKey' => ['%project_edition%']];

        $config = $projectEditionAwareConfigurationEditor->set($initialConfig, 'testKey.testSection', ['testValue1', '%project_edition%']);

        Assert::assertEquals(['testKey' => [$expectedResult, 'testSection' => ['testValue1', $expectedResult]]], $config);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testReplacesValueWhenAppendIsUsed(
        ProjectEditionAwareConfigurationEditor $projectEditionAwareConfigurationEditor,
        string $expectedResult): void
    {
        $initialConfig = ['testKey' => '%project_edition%'];

        $config = $projectEditionAwareConfigurationEditor->append($initialConfig, 'testKey', '%project_edition%');

        Assert::assertEquals(['testKey' => [$expectedResult, $expectedResult]], $config);
    }
}
