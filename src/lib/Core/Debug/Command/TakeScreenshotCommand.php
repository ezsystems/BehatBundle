<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Core\Debug\Command;

use Behat\Mink\Session;
use Psy\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TakeScreenshotCommand extends Command
{
    /** @var \Behat\Mink\Session */
    protected $session;

    public function __construct(Session $session)
    {
        parent::__construct();
        $this->session = $session;
    }

    protected function configure()
    {
        $this
            ->setName('screenshot')
            ->setDefinition([])
            ->setDescription('Takes a screenshot of the currently opened website')
            ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $image = $this->session->getScreenshot();
        $filePath = sys_get_temp_dir() . \DIRECTORY_SEPARATOR . uniqid('debug') . '.png';

        file_put_contents($filePath, $image);
        $output->writeln(sprintf('Screenshot saved to "%s"', $filePath));

        return 0;
    }
}
