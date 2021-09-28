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

class ShowURLCommand extends Command
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
            ->setName('url')
            ->setDefinition([])
            ->setDescription('Displays the URL of the currently opened website')
            ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->session->getCurrentUrl());

        return 0;
    }
}
