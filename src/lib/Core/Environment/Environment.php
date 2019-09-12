<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\Core\Environment;

use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Environment
{
    /** @var \Symfony\Component\DependencyInjection\ContainerInterface Symfony DI service container */
    private $serviceContainer;

    /**
     * Environment constructor.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $serviceContainer
     */
    public function __construct(ContainerInterface $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
    }

    public function getInstallType(): int
    {
        $projectDir = $this->serviceContainer->getParameter('kernel.project_dir');
        $composerJsonPath = realpath($projectDir . '/composer.json');
        if (false === $composerJsonPath) {
            throw new RuntimeException(
                "Unable to find composer.json in {$projectDir} to determine meta repository and installation type"
            );
        }

        $composerConfig = json_decode(file_get_contents($composerJsonPath));
        if (!isset(InstallType::PACKAGE_NAME_MAP[$composerConfig->name])) {
            throw new RuntimeException("Unknown installation type for the package: {$composerConfig->name}");
        }

        return InstallType::PACKAGE_NAME_MAP[$composerConfig->name];
    }
}
