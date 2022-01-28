<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Subscriber;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use EzSystems\Behat\API\ContentData\ContentDataProvider;
use EzSystems\Behat\API\ContentData\RandomDataGenerator;
use EzSystems\Behat\Event\Events;
use EzSystems\Behat\Event\TransitionEvent;
use Ibexa\Workflow\Behat\Facade\WorkflowFacade;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class EditContent extends AbstractProcessStage implements EventSubscriberInterface
{
    /** @var \EzSystems\Behat\API\ContentData\ContentDataProvider */
    private $contentDataProvider;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Workflow\Behat\Facade\WorkflowFacade */
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

    public function __construct(
        LoggerInterface $logger,
        ContentDataProvider $contentDataProvider,
        ContentService $contentService,
        EventDispatcherInterface $eventDispatcher,
        UserService $userService,
        PermissionResolver $permissionResolver,
        WorkflowFacade $workflowFacade,
        RandomDataGenerator $randomDataGenerator
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
        $language = $this->getRandomValue($event->availableLanguages);
        $this->randomDataGenerator->setLanguage($language);

        $contentDraft = $this->contentService->createContentDraft($event->content->contentInfo);
        $contentUpdateStruct = $this->contentDataProvider->getRandomContentUpdateData($event->mainLanguage, $language);
        $event->content = $this->contentService->updateContent($contentDraft->getVersionInfo(), $contentUpdateStruct);

        $transitionName = 'to_review';
        $this->workflowFacade->transition($event->content, $transitionName, $this->randomDataGenerator->getRandomTextLine());
    }
}
