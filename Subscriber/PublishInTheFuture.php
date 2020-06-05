<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\BehatBundle\Subscriber;

use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;
use EzSystems\BehatBundle\API\ContentData\RandomDataGenerator;
use EzSystems\BehatBundle\API\Facade\WorkflowFacade;
use EzSystems\BehatBundle\Event\Events;
use EzSystems\BehatBundle\Event\TransitionEvent;
use EzSystems\DateBasedPublisher\API\Repository\DateBasedPublisherServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PublishInTheFuture extends AbstractProcessStage implements EventSubscriberInterface
{
    /** @var DateBasedPublisherServiceInterface */
    private $dateBasedPublisherService;

    /** @var \EzSystems\BehatBundle\API\Facade\WorkflowFacade */
    private $workflowFacade;

    public function __construct(EventDispatcher $eventDispatcher,
                                UserService $userService,
                                PermissionResolver $permissionResolver,
                                LoggerInterface $logger,
                                RandomDataGenerator $randomDataGenerator,
                                DateBasedPublisherServiceInterface $dateBasedPublisherService,
                                WorkflowFacade $workflowFacade)
    {
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
        $this->dateBasedPublisherService->scheduleVersion($event->content->versionInfo, $this->randomDataGenerator->getRandomDateInTheFuture(), '');
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::REVIEW_TO_PUBLISH_LATER => 'execute',
        ];
    }
}
