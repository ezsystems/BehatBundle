<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\BehatBundle\Command;

use EzSystems\BehatBundle\Event\InitialEvent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Yaml\Yaml;

class CreateExampleDataManagerCommand extends Command
{
    private const BATCH_SIZE = 100;

    /** @var Stopwatch */
    private $stopwatch;

    /** @var string */
    private $env;

    /** @var string */
    private $projectDir;

    /** @var array */
    private $processes;

    /** @var Serializer */
    private $serializer;

    public function __construct(string $env, string $projectDir)
    {
        parent::__construct('ezplatform:tools:generate-items');
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
        $this->env = $env;
        $this->projectDir = $projectDir;
        $this->stopwatch = new Stopwatch();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $data = $this->getData();
        $this->stopwatch->start('timer');

        $this->createProcesses($output, $data);

        $event = $this->stopwatch->stop('timer');
        $output->writeln(sprintf('Duration: %d s, memory: %s MB', $event->getDuration() / 1000, $event->getMemory() / 1024 / 1024));
    }

    private function executeCommand(OutputInterface $output, $cmd, float $timeout = 1200)
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

    private function getData()
    {
        $data = Yaml::parseFile(sprintf('%s/vendor/ezsystems/behatbundle/EzSystems/BehatBundle/features/setup/volume/data.yaml', $this->projectDir));

        return $data['countries'];
    }

    private function createProcesses(OutputInterface $output, $data)
    {
        foreach ($data as $row) {
            $eventData = $this->parseData($row);
            $command = sprintf('%s %d %s',  CreateExampleDataCommand::NAME, self::BATCH_SIZE, $this->serialize($eventData));
            $this->processes[] = $this->executeCommand($output, $command);
        }

        while (count($this->processes)) {
            foreach ($this->processes as $i => $runningProcess) {
                if (!$runningProcess->isRunning()) {
                    unset($this->processes[$i]);
                }
                sleep(1);
            }
        }
    }

    private function parseData($dataRow): InitialEvent
    {
        $country = array_key_first($dataRow);
        $dataRow = $dataRow[$country];

        return new InitialEvent($country, $dataRow['editors'], $dataRow['subtreePath'], $dataRow['languages'], $dataRow['contentTypes']);
    }

    private function serialize(InitialEvent $eventData): string
    {
        return base64_encode($this->serializer->serialize($eventData, 'json'));
    }
}
