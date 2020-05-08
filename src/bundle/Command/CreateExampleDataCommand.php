<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);
namespace EzSystems\BehatBundle\Command;

use EzSystems\Behat\Event\TransitionEvent;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CreateExampleDataCommand extends Command
{
    private $eventDispatcher;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(EventDispatcherInterface $eventDispatcher, LoggerInterface $logger)
    {
        parent::__construct();
        $this->stopwatch = new Stopwatch();
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this->setName('ezplatform:tools:create-data');
        $this
            ->addArgument(
            'iterations',
            InputArgument::REQUIRED
            )->addArgument(
                'editors',
                InputArgument::REQUIRED
            )->addArgument(
                'language',
                InputArgument::REQUIRED
            )->addArgument(
            'parentPath',
            InputArgument::REQUIRED
            )->addArgument(
        'contentTypeIdentifiers',
        InputArgument::REQUIRED
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $iterations = $input->getArgument('iterations');
        $editors = explode(',', $input->getArgument('editors'));
        $contentTypeData = $this->parseContentTypeData($input->getArgument('contentTypeIdentifiers'));
        $parentPath = $input->getArgument('parentPath');
        $language = $input->getArgument('language');

        $stats = sprintf('Starting: %s %s %s', implode(',', $editors), $parentPath, $language);
        $this->logger->log(LogLevel::INFO, $stats);
        $this->stopwatch->start('phase');

        $threshhold = 0;
        foreach ($contentTypeData as $contentTypeIdentifier => $probability) {
            for ($i = 0; $i < $iterations; ++$i) {
                if ($threshhold <= $i && $i < $threshhold + $probability * $iterations) {
                    $this->eventDispatcher->dispatch(
                        new TransitionEvent(
                            $this->getRandomValue($editors, 5),
                            $contentTypeIdentifier,
                            $parentPath,
                            $language
                        ),
                        TransitionEvent::START
                    );
                }
            }
            $threshhold += $probability * $iterations;
        }

        $event = $this->stopwatch->stop('phase');
        $statsEnd = sprintf('Ending, duration: %d s, memory: %s MB', $event->getDuration() / 1000, $event->getMemory() / 1024 / 1024);
        $this->logger->log(LogLevel::INFO, $statsEnd);

        return 0;
    }

    private function parseContentTypeData(string $data): array
    {
        $result = [];
        $contentTypes = explode(',', $data);
        foreach ($contentTypes as $contentType) {
            $contentTypeIdentifier = explode(':', $contentType)[0];
            $probability = explode(':', $contentType)[1];

            $result[$contentTypeIdentifier] = $probability;
        }

        return $result;
    }

    public function getRandomValue(array $values, int $count): string
    {
        return $values[random_int(0, $count - 1)];
    }
}
