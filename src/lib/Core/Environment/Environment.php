<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\Core\Environment;

use EzSystems\PlatformInstallerBundle\Installer\Installer;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class Environment
{
    /** @var \Symfony\Component\DependencyInjection\ContainerInterface Symfony DI service container */
    private $serviceContainer;

    /** @var array Names of available installer services in Studio */
    private $installerServices = ['platform' => 'ezplatform.installer.clean_installer',
        'platform-demo' => 'app.installer.demo_installer',
        'platform-ee' => 'ezstudio.installer.studio_installer',
        'platform-ee-demo' => 'app.installer.ee-demo_installer', ];

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
        if ($this->serviceContainer->has($this->installerServices['platform-ee-demo'])) {
            return InstallType::ENTERPRISE_DEMO;
        }

        if ($this->serviceContainer->has($this->installerServices['platform-ee'])) {
            return InstallType::ENTERPRISE;
        }

        if ($this->serviceContainer->has($this->installerServices['platform-demo'])) {
            return InstallType::PLATFORM_DEMO;
        }

        if ($this->serviceContainer->has($this->installerServices['platform'])) {
            return InstallType::PLATFORM;
        }
    }
}
