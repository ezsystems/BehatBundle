<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\BehatBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestSiteaccessCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('ez:behat:siteaccess');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->getContainer()->get('ezpublish.siteaccess')->name);
    }
}
