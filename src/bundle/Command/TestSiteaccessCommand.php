<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\BehatBundle\Command;

use eZ\Bundle\EzPublishCoreBundle\Command\BackwardCompatibleCommand;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestSiteaccessCommand extends Command implements BackwardCompatibleCommand
{
    /** @var \eZ\Publish\Core\MVC\Symfony\SiteAccess */
    private $siteaccess;

    public function __construct(SiteAccess $siteaccess)
    {
        $this->siteaccess = $siteaccess;

        parent::__construct();
    }

    /**
     * @return string[]
     */
    public function getDeprecatedAliases(): array
    {
        return ['ez:behat:siteaccess'];
    }

    protected function configure()
    {
        $this
            ->setName('ibexa:behat:test-siteaccess')
            ->setAliases($this->getDeprecatedAliases())
            ->setDescription('Outputs the name of the active siteaccess')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln($this->siteaccess->name);

        return 0;
    }
}
