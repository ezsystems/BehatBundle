<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Core\Configuration;

use Ibexa\Behat\API\Facade\ContentFacade;

class LocationAwareConfigurationEditor implements ConfigurationEditorInterface
{
    /** @var \Ibexa\Behat\Core\Configuration\ConfigurationEditorInterface */
    private $innerConfigurationEditor;

    /** @var \Ibexa\Behat\API\Facade\ContentFacade */
    private $contentFacade;

    public function __construct(ConfigurationEditorInterface $innerConfigurationEditor, ContentFacade $contentFacade)
    {
        $this->innerConfigurationEditor = $innerConfigurationEditor;
        $this->contentFacade = $contentFacade;
    }

    public function append($config, string $key, $value)
    {
        $config = $this->innerConfigurationEditor->append($config, $key, $value);

        return $this->resolveLocationReference($config);
    }

    public function set($config, string $key, $value)
    {
        $config = $this->innerConfigurationEditor->set($config, $key, $value);

        return $this->resolveLocationReference($config);
    }

    public function get($config, string $key)
    {
        $config = $this->resolveLocationReference($config);

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

    private function resolveLocationReference($config)
    {
        array_walk_recursive($config, [$this, 'replaceSingleValue']);

        return $config;
    }

    private function replaceSingleValue(&$value): void
    {
        if (!is_string($value)) {
            return;
        }

        $pattern = '%location_id\\((.*)\\)%';
        $matches = [];

        if (1 === preg_match($pattern, $value, $matches)) {
            $value = $this->contentFacade->getLocationByLocationURL($matches[1])->id;
        }
    }
}

class_alias(LocationAwareConfigurationEditor::class, 'EzSystems\Behat\Core\Configuration\LocationAwareConfigurationEditor');
