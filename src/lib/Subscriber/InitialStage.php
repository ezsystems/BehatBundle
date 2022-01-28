<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Subscriber;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use EzSystems\Behat\API\ContentData\RandomDataGenerator;
use EzSystems\Behat\API\Facade\SearchFacade;
use EzSystems\Behat\Event\Events;
use EzSystems\Behat\Event\InitialEvent;
use EzSystems\Behat\Event\TransitionEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InitialStage extends AbstractProcessStage implements EventSubscriberInterface
{
    /** @var \EzSystems\Behat\API\Facade\SearchFacade */
    private $searchFacade;

    protected function getTransitions(): array
    {
        return [
            Events::START_TO_DRAFT => 1,
        ];
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::START => 'onStart',
        ];
    }

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        UserService $userService,
        PermissionResolver $permissionResolver,
        LoggerInterface $logger,
        SearchFacade $searchFacade,
        RandomDataGenerator $randomDataGenerator
    ) {
        parent::__construct($eventDispatcher, $userService, $permissionResolver, $logger, $randomDataGenerator);
        $this->searchFacade = $searchFacade;
    }

    private function getContentType($contentTypesData): string
    {
        $randomNumber = $this->randomDataGenerator->getRandomProbability();
        $threshold = 0;
        foreach ($contentTypesData as $contentTypeData) {
            $contentTypeIdentifier = array_key_first($contentTypeData);
            $probability = $contentTypeData[$contentTypeIdentifier]['probability'];
            if ($threshold <= $randomNumber && $randomNumber < $threshold + $probability) {
                return $contentTypeIdentifier;
            }

            $threshold += $probability;
        }

        throw new \Exception('Could not determine contentTypeIdentifier.');
    }

    private function getLocationPath(string $subtreePath): string
    {
        return $this->searchFacade->getRandomChildFromPath($subtreePath);
    }

    public function onStart(InitialEvent $event): void
    {
        $contentTypeIdentifier = $this->getContentType($event->contentTypes);
        $locationPath = $this->getLocationPath($event->subtreePath);

        $transitionEvent = new TransitionEvent($event->editors, $contentTypeIdentifier, $locationPath, $event->languages, $event->mainLanguage);

        $this->transitionToNextStage($transitionEvent);
    }

    protected function doExecute(TransitionEvent $event): void
    {
    }
}
