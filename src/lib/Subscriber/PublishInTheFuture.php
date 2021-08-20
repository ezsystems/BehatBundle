<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Subscriber;

use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;
use EzSystems\Behat\API\ContentData\RandomDataGenerator;
use EzSystems\Behat\Event\Events;
use EzSystems\Behat\Event\TransitionEvent;
use EzSystems\DateBasedPublisher\API\Repository\DateBasedPublishServiceInterface;
use Ibexa\Workflow\Behat\Facade\WorkflowFacade;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PublishInTheFuture extends AbstractProcessStage implements EventSubscriberInterface
{
    /** @var \EzSystems\DateBasedPublisher\API\Repository\DateBasedPublishServiceInterface */
    private $dateBasedPublisherService;

    /** @var \Ibexa\Workflow\Behat\Facade\WorkflowFacade */
    private $workflowFacade;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        UserService $userService,
        PermissionResolver $permissionResolver,
        LoggerInterface $logger,
        RandomDataGenerator $randomDataGenerator,
        DateBasedPublishServiceInterface $dateBasedPublisherService,
        WorkflowFacade $workflowFacade
    ) {
        parent::__construct($eventDispatcher, $userService, $permissionResolver, $logger, $randomDataGenerator);

        $this->dateBasedPublisherService = $dateBasedPublisherService;
        $this->workflowFacade = $workflowFacade;
    }

    protected function getTransitions(): array
    {
        return [
            Events::PUBLISH_LATER_TO_END => 1,
        ];
    }

    protected function doExecute(TransitionEvent $event): void
    {
        $transitionName = 'done';
        $this->workflowFacade->transition($event->content, $transitionName, $this->randomDataGenerator->getRandomTextLine());
        $this->dateBasedPublisherService->schedulePublish($event->content->versionInfo, $this->randomDataGenerator->getRandomDateInTheFuture());
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::REVIEW_TO_PUBLISH_LATER => 'execute',
        ];
    }
}
