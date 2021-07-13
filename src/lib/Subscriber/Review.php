<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Subscriber;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;
use EzSystems\Behat\API\ContentData\ContentDataProvider;
use EzSystems\Behat\API\ContentData\RandomDataGenerator;
use EzSystems\Behat\Event\Events;
use EzSystems\Behat\Event\TransitionEvent;
use Ibexa\Workflow\Tests\Behat\Facade\WorkflowFacade;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Review extends AbstractProcessStage implements EventSubscriberInterface
{
    /** @var \EzSystems\Behat\API\ContentData\ContentDataProvider */
    private $contentDataProvider;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Workflow\Tests\Behat\Facade\WorkflowFacade */
    private $workflowFacade;

    protected function getTransitions(): array
    {
        return [
            Events::REVIEW_TO_END => 0.01,
            Events::REVIEW_TO_REVIEW => 0.18,
            Events::REVIEW_TO_PUBLISH_LATER => 0.01,
            Events::REVIEW_TO_PUBLISH => 0.80,
        ];
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::DRAFT_TO_REVIEW => 'execute',
            Events::REVIEW_TO_REVIEW => 'execute',
            Events::EDIT_TO_REVIEW => 'execute',
        ];
    }

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        UserService $userService,
        PermissionResolver $permissionResolver,
        LoggerInterface $logger,
        ContentDataProvider $contentDataProvider,
        ContentService $contentService,
        RandomDataGenerator $randomDataGenerator,
        WorkflowFacade $workflowFacade
    ) {
        parent::__construct($eventDispatcher, $userService, $permissionResolver, $logger, $randomDataGenerator);
        $this->contentDataProvider = $contentDataProvider;
        $this->contentService = $contentService;
        $this->workflowFacade = $workflowFacade;
    }

    protected function doExecute(TransitionEvent $event): void
    {
        $this->contentDataProvider->setContentTypeIdentifier($event->contentTypeIdentifier);
        $this->setCurrentUser($this->getRandomValue($event->editors));
        $contentUpdateStruct = $this->contentDataProvider->getRandomContentUpdateData($event->mainLanguage, $event->mainLanguage);
        $event->content = $this->contentService->updateContent($event->content->getVersionInfo(), $contentUpdateStruct);

        $transitionName = 're_review';
        $this->workflowFacade->transition($event->content, $transitionName, $this->randomDataGenerator->getRandomTextLine());
    }
}
