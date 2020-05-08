<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\BehatBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Symfony\Component\Stopwatch\Stopwatch;

class CreateExampleDataManagerCommand extends Command
{
    // @TODO: Move to yaml?
    private $DATA = [
        'contentTypeIdentifiers' => [
            ['short_article', 0.75],
            ['long_article', 0.25],
        ],
        'country' => [
            'france' => [
                'editors' => ['FrenchEditor1', 'FrenchEditor2', 'FrenchEditor3', 'FrenchEditor4', 'FrenchEditor5'],
                'subtreePath' => '/Europe/France',
                'language' => 'fre-FR',
            ],
            'germany' => [
                'editors' => ['GermanEditor1', 'GermanEditor2', 'GermanEditor3', 'GermanEditor4', 'GermanEditor5'],
                'subtreePath' => '/Europe/Germany',
                'language' => 'ger-DE',
            ],
            'england' => [
                'editors' => ['EnglishEditor1', 'EnglishEditor2', 'EnglishEditor3', 'EnglishEditor4', 'EnglishEditor5'],
                'subtreePath' => '/Europe/England',
                'language' => 'eng-GB',
            ],
            'poland' => [
                'editors' => ['PolishEditor1', 'PolishEditor2', 'PolishEditor3', 'PolishEditor4', 'PolishEditor5'],
                'subtreePath' => '/Europe/Poland',
                'language' => 'pol-PL',
            ],
            'italy' => [
                'editors' => ['ItalianEditor1', 'ItalianEditor2', 'ItalianEditor3', 'ItalianEditor4', 'ItalianEditor5'],
                'subtreePath' => '/Europe/Italy',
                'language' => 'ita-IT',
            ],
            'spain' => [
                'editors' => ['SpanishEditor1', 'SpanishEditor2', 'SpanishEditor3', 'SpanishEditor4', 'SpanishEditor5'],
                'subtreePath' => '/Europe/Spain',
                'language' => 'esl-ES',
            ],
            'malta' => [
                'editors' => ['MalteseEditor1', 'MalteseEditor2', 'MalteseEditor3', 'MalteseEditor4', 'MalteseEditor5'],
                'subtreePath' => '/Europe/Malta',
                'language' => 'eng-GB',
            ],
            'austria' => [
                'editors' => ['AustrianEditor1', 'AustrianEditor2', 'AustrianEditor3', 'AustrianEditor4', 'AustrianEditor5'],
                'subtreePath' => '/Europe/Austria',
                'language' => 'ger-DE',
            ],
            'switzerland' => [
                'editors' => ['SwissEditor1', 'SwissEditor2', 'SwissEditor3', 'SwissEditor4', 'SwissEditor5'],
                'subtreePath' => '/Europe/Switzerland',
                'language' => 'ger-DE',
            ],
            'portugal' => [
                'editors' => ['PortugueseEditor1', 'PortugueseEditor2', 'PortugueseEditor3', 'PortugueseEditor4', 'PortugueseEditor5'],
                'subtreePath' => '/Europe/Portugal',
                'language' => 'por-PT',
            ],
        ],
    ];

    /** @var Stopwatch */
    private $stopwatch;

    /** @var string */
    private $env;

    /** @var EventDispatcher */
    private $eventDispatcher;

    public function __construct(string $env, $name = null)
    {
        parent::__construct($name);
        $this->env = $env;
        $this->stopwatch = new Stopwatch();
    }

    public function configure()
    {
        $this->setName('ezplatform:tools:generate-items');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $processes = [];

        $this->stopwatch->start('timer');

        foreach ($this->DATA['country'] as $key => $values) {
            $editors = implode(',', $values['editors']);
            $parentPath = $values['subtreePath'];
            $language = $values['language'];

            $command = sprintf('%s %s %s %s %s %s',
                'ezplatform:tools:create-data',
                100,
                $editors,
                $language,
                $parentPath,
                $this->parseContentTypes($this->DATA['contentTypeIdentifiers'])
            );
            $processes[] = $this->executeCommand($output, $command);
        }

        while (count($processes)) {
            foreach ($processes as $i => $runningProcess) {
                // specific process is finished, so we remove it
                if (!$runningProcess->isRunning()) {
                    unset($processes[$i]);
                }

                // check every second
                sleep(1);
            }
        }

        $event = $this->stopwatch->stop('timer');

        $output->writeln(sprintf('Duration: %d s, memory: %s MB', $event->getDuration() / 1000, $event->getMemory() / 1024 / 1024));
    }

    public function getRandomValue(array $values, int $count): string
    {
        return $values[random_int(0, $count - 1)];
    }

    private function executeCommand(OutputInterface $output, $cmd, $timeout = 1200)
    {
        $phpFinder = new PhpExecutableFinder();
        if (!$phpPath = $phpFinder->find(false)) {
            throw new \RuntimeException('The php executable could not be found, add it to your PATH environment variable and try again');
        }

        // We don't know which php arguments where used so we gather some to be on the safe side
        $arguments = $phpFinder->findArguments();
        if (false !== ($ini = php_ini_loaded_file())) {
            $arguments[] = '--php-ini=' . $ini;
        }

        // Pass memory_limit in case this was specified as php argument, if not it will most likely be same as $ini.
        if ($memoryLimit = ini_get('memory_limit')) {
            $arguments[] = '-d memory_limit=' . $memoryLimit;
        }

        $phpArgs = implode(' ', array_map('escapeshellarg', $arguments));
        $php = escapeshellarg($phpPath) . ($phpArgs ? ' ' . $phpArgs : '');

        // Make sure to pass along relevant global Symfony options to console command
        $console = escapeshellarg('bin/console');
        if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
            $console .= ' -' . str_repeat('v', $output->getVerbosity() - 1);
        }

        if ($output->isDecorated()) {
            $console .= ' --ansi';
        }

        $console .= ' --env=' . escapeshellarg($this->env);

        $process = new Process($php . ' ' . $console . ' ' . $cmd, null, null, null, $timeout);
        $process->start(function ($type, $buffer) use ($output) { $output->write($buffer, false); });

        return $process;
    }

    private function parseContentTypes(array $contentTypeData): string
    {
        $results = [];
        foreach ($contentTypeData as $contentType) {
            $results[] = implode(':', $contentType);
        }

        return implode(',', $results);
    }
}
