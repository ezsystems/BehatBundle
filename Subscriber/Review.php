<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\BehatBundle\Subscriber;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;
use EzSystems\BehatBundle\API\ContentData\ContentDataProvider;
use EzSystems\BehatBundle\API\ContentData\RandomDataGenerator;
use EzSystems\BehatBundle\API\Facade\WorkflowFacade;
use EzSystems\BehatBundle\Event\Events;
use EzSystems\BehatBundle\Event\TransitionEvent;
use EzSystems\EzPlatformWorkflow\Service\WorkflowService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Review extends AbstractProcessStage implements EventSubscriberInterface
{

    /** @var ContentDataProvider */
    private $contentDataProvider;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \EzSystems\BehatBundle\API\Facade\WorkflowFacade */
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

    public function __construct(EventDispatcher $eventDispatcher,
                                UserService $userService,
                                PermissionResolver $permissionResolver,
                                LoggerInterface $logger,
                                ContentDataProvider $contentDataProvider,
                                ContentService $contentService,
                                RandomDataGenerator $randomDataGenerator,
                                WorkflowFacade $workflowFacade)
    {
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