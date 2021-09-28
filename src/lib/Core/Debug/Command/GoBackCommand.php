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

class GoBackCommand extends Command
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
            ->setName('back')
            ->setDefinition([])
            ->setDescription("Goes back one page in browser's history")
            ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->session->back();

        $output->writeln("The last page from browser's history has been visited.");

        return 0;
    }
}
