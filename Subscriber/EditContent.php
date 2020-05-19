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
use EzSystems\EzPlatformWorkflow\Registry\WorkflowRegistryInterface;
use EzSystems\EzPlatformWorkflow\Service\WorkflowService;
use EzSystems\EzPlatformWorkflow\Service\WorkflowServiceInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EditContent extends AbstractProcessStage implements EventSubscriberInterface
{
    /** @var \EzSystems\BehatBundle\API\ContentData\ContentDataProvider */
    private $contentDataProvider;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \EzSystems\BehatBundle\API\Facade\WorkflowFacade */
    private $workflowFacade;

    protected function getTransitions(): array
    {
        return [
            Events::EDIT_TO_REVIEW => 1,
        ];
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::PUBLISH_TO_EDIT => 'execute',
        ];
    }

    public function __construct(LoggerInterface $logger,
                                ContentDataProvider $contentDataProvider,
                                ContentService $contentService,
                                EventDispatcher $eventDispatcher,
                                UserService $userService,
                                PermissionResolver $permissionResolver,
                                WorkflowFacade $workflowFacade,
                                RandomDataGenerator $randomDataGenerator)
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
        $language = $this->getRandomValue($event->availableLanguages);
        $this->randomDataGenerator->setLanguage($language);

        $contentDraft = $this->contentService->createContentDraft($event->content->contentInfo);
        $contentUpdateStruct = $this->contentDataProvider->getRandomContentUpdateData($event->mainLanguage, $language);
        $event->content = $this->contentService->updateContent($contentDraft->getVersionInfo(), $contentUpdateStruct);

        // $transitionName = 'to_review';
        // $this->workflowFacade->transition($event->content, $transitionName, $this->randomDataGenerator->getRandomTextLine());
    }
}
