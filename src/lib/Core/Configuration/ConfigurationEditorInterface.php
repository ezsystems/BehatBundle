<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\Core\Configuration;

interface ConfigurationEditorInterface
{
    /**
     * Appends given value under key, extending existing settings.
     *
     * @param $config
     * @param string $key 'key' or 'nested.key'
     * @param string|array $value
     *
     * @return mixed YAML config
     */
    public function append($config, string $key, $value);

    /**
     * Sets given value under key. Existing settings are overwritten.
     *
     * @param $config
     * @param string $key 'key' or 'nested.key'
     * @param string|array $value
     *
     * @return mixed YAML config
     */
    public function set($config, string $key, $value);

    public function get($config, string $key);

    /**
     * @param string $filePath
     *
     * @return mixed YAML config
     */
    public function getConfigFromFile(string $filePath);

    /**
     * @param $filePath
     * @param $config
     */
    public function saveConfigToFile($filePath, $config): void;
}
