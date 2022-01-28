<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Core\Configuration;

use RuntimeException;

class ProjectEditionAwareConfigurationEditor implements ConfigurationEditorInterface
{
    /** @var \EzSystems\Behat\Core\Configuration\ConfigurationEditorInterface */
    private $innerConfigurationEditor;

    private const PROJECT_EDITION_STRING = '%project_edition%';
    private string $projectDir;

    private const PROJECT_EDITION_MAP = [
        'commerce' => 'ibexa/commerce',
        'experience' => 'ibexa/experience',
        'content' => 'ibexa/content',
        'oss' => 'ibexa/oss',
    ];

    public function __construct(ConfigurationEditorInterface $innerConfigurationEditor, string $projectDir)
    {
        $this->innerConfigurationEditor = $innerConfigurationEditor;
        $this->projectDir = $projectDir;
    }

    public function append($config, string $key, $value)
    {
        $config = $this->innerConfigurationEditor->append($config, $key, $value);

        return $this->resolveProjectEditionReference($config);
    }

    public function set($config, string $key, $value)
    {
        $config = $this->innerConfigurationEditor->set($config, $key, $value);

        return $this->resolveProjectEditionReference($config);
    }

    public function get($config, string $key)
    {
        $config = $this->resolveProjectEditionReference($config);

        return $this->innerConfigurationEditor->get($config, $key);
    }

    public function getConfigFromFile(string $filePath)
    {
        return $this->innerConfigurationEditor->getConfigFromFile($filePath);
    }

    public function saveConfigToFile($filePath, $config): void
    {
        $this->innerConfigurationEditor->saveConfigToFile($filePath, $config);
    }

    private function resolveProjectEditionReference($config)
    {
        array_walk_recursive($config, [$this, 'replaceSingleValue']);

        return $config;
    }

    private function replaceSingleValue(&$value): void
    {
        if (!is_string($value)) {
            return;
        }

        if (strpos($value, self::PROJECT_EDITION_STRING) !== false) {
            $value = str_replace($value, $this->getProjectEdition(), self::PROJECT_EDITION_STRING);
        }
    }

    private function getProjectEdition(): string
    {
        $composerJsonPath = realpath($this->projectDir . '/composer.json');
        if (false === $composerJsonPath) {
            throw new RuntimeException(
                "Unable to find composer.json in {$this->projectDir} to determine project edition"
            );
        }

        $composerConfig = json_decode(file_get_contents($composerJsonPath), true, 512, JSON_THROW_ON_ERROR);

        foreach (self::PROJECT_EDITION_MAP as $edition => $package) {
            if (array_key_exists($package, $composerConfig['require'])) {
                return $edition;
            }
        }

        throw new RuntimeException(
            'None of the packages defined in composer.json matched defined project editions!'
        );
    }
}
