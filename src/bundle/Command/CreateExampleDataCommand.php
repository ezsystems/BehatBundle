<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Behat\Command;

use Ibexa\Behat\Event\Events;
use Ibexa\Behat\Event\InitialEvent;
use Ibexa\Bundle\Core\Command\BackwardCompatibleCommand;
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

class CreateExampleDataCommand extends Command implements BackwardCompatibleCommand
{
    public const NAME = 'ibexa:behat:create-data';

    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \Symfony\Component\Serializer\Serializer */
    private $serializer;

    /** @var \Symfony\Component\Stopwatch\Stopwatch */
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
            ->setAliases(['ezplatform:tools:create-data'])
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

    /**
     * @return string[]
     */
    public function getDeprecatedAliases(): array
    {
        return ['ezplatform:tools:create-data'];
    }
}

class_alias(CreateExampleDataCommand::class, 'EzSystems\BehatBundle\Command\CreateExampleDataCommand');
