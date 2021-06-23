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
use EzSystems\Behat\API\Facade\ContentFacade;
use EzSystems\Behat\Event\Events;
use EzSystems\Behat\Event\TransitionEvent;
use Ibexa\Workflow\Tests\Behat\Facade\WorkflowFacade;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CreateContentDraft extends AbstractProcessStage implements EventSubscriberInterface
{
    /** @var \EzSystems\Behat\API\Facade\ContentFacade */
    private $contentFacade;

    /** @var \Ibexa\Workflow\Tests\Behat\Facade\WorkflowFacade */
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
