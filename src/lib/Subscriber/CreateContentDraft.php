<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Subscriber;

use Ibexa\Behat\API\ContentData\RandomDataGenerator;
use Ibexa\Behat\API\Facade\ContentFacade;
use Ibexa\Behat\Event\Events;
use Ibexa\Behat\Event\TransitionEvent;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Workflow\Behat\Facade\WorkflowFacade;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CreateContentDraft extends AbstractProcessStage implements EventSubscriberInterface
{
    /** @var \Ibexa\Behat\API\Facade\ContentFacade */
    private $contentFacade;

    /** @var \Ibexa\Workflow\Behat\Facade\WorkflowFacade */
    private $workflowFacade;

    public static function getSubscribedEvents()
    {
        return [
            Events::START_TO_DRAFT => ['execute', 0],
        ];
    }

    protected function getTransitions(): array
    {
        return [
            Events::DRAFT_TO_REVIEW => 1,
        ];
    }

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        UserService $userService,
        PermissionResolver $permissionResolver,
        LoggerInterface $logger,
        ContentFacade $contentFacade,
        RandomDataGenerator $randomDataGenerator,
        WorkflowFacade $workflowFacade
    ) {
        parent::__construct($eventDispatcher, $userService, $permissionResolver, $logger, $randomDataGenerator);
        $this->contentFacade = $contentFacade;
        $this->workflowFacade = $workflowFacade;
    }

    public function doExecute(TransitionEvent $event): void
    {
        $event->author = $this->getRandomValue($event->editors);
        $this->setCurrentUser($event->author);
        $this->randomDataGenerator->setLanguage($event->mainLanguage);
        $event->content = $this->contentFacade->createContentDraft($event->contentTypeIdentifier, $event->locationPath, $event->mainLanguage);

        $transitionName = 'to_review';
        $this->workflowFacade->transition($event->content, $transitionName, $this->randomDataGenerator->getRandomTextLine());
    }
}

class_alias(CreateContentDraft::class, 'EzSystems\Behat\Subscriber\CreateContentDraft');
