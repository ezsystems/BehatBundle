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

    /** @var array */
    private $metarepositoryPackageNameMap = [
        'ezsystems/ezplatform' => InstallType::OSS,
        'ezsystems/ezplatform-ee' => InstallType::EXPERIENCE,
        'ezsystems/ezcommerce' => InstallType::COMMERCE,
    ];

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
        if ($this->isInstalledFromMetarepository($composerConfig)) {
            return $this->getInstallTypeFromMetarepository($composerConfig);
        }

        $packages = $composerConfig->require;

        $installTypeMap = [
            'ibexa/commerce' => InstallType::COMMERCE,
            'ibexa/experience' => InstallType::EXPERIENCE,
            'ibexa/content' => InstallType::CONTENT,
            'ibexa/oss' => InstallType::OSS,
        ];

        foreach ($installTypeMap as $expectedProperty => $installType) {
            if (property_exists($packages, $expectedProperty)) {
                return $installType;
            }
        }
    }

    private function isInstalledFromMetarepository($composerConfig): bool
    {
        return property_exists($composerConfig, 'name') && \in_array($composerConfig->name, \array_keys($this->metarepositoryPackageNameMap));
    }

    private function getInstallTypeFromMetarepository($composerConfig): int
    {
        return $this->metarepositoryPackageNameMap[$composerConfig->name];
    }
}
