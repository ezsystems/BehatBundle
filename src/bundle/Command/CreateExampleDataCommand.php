<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);
namespace EzSystems\BehatBundle\Command;

use EzSystems\Behat\Event\Events;
use EzSystems\Behat\Event\InitialEvent;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CreateExampleDataCommand extends Command
{
    public const NAME = 'ezplatform:tools:create-data';

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var LoggerInterface */
    private $logger;

    /** @var Serializer */
    private $serializer;

    /** @var Stopwatch */
    private $stopwatch;

    public function __construct(EventDispatcherInterface $eventDispatcher, LoggerInterface $logger)
    {
        parent::__construct(self::NAME);
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
        $this->stopwatch = new Stopwatch();
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this
            ->addArgument('iterations', InputArgument::REQUIRED)
            ->addArgument('serializedTransitionData', InputArgument::REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $iterations = $input->getArgument('iterations');
        $initialData = $this->parseInputData($input->getArgument('serializedTransitionData'));

        $stats = sprintf('Starting: %s', $initialData->country);

        $this->logger->log(LogLevel::INFO, $stats);
        $output->writeln($stats);
        $this->stopwatch->start('phase');

        for ($i = 0; $i < $iterations; ++$i) {
            $this->eventDispatcher->dispatch($initialData, Events::START);
        }

        $event = $this->stopwatch->stop('phase');

        $statsEnd = sprintf('Ending %s, duration: %d s, memory: %s MB', $initialData->country, $event->getDuration() / 1000, $event->getMemory() / 1024 / 1024);
        $this->logger->log(LogLevel::INFO, $statsEnd);
        $output->writeln($statsEnd);

        return 0;
    }

    private function parseInputData(string $serializedTransitionEvent): InitialEvent
    {
        return $this->serializer->deserialize(base64_decode($serializedTransitionEvent), InitialEvent::class, 'json');
    }
}
