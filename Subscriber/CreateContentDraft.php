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
use EzSystems\BehatBundle\API\Facade\ContentFacade;
use EzSystems\BehatBundle\API\Facade\WorkflowFacade;
use EzSystems\BehatBundle\Event\Events;
use EzSystems\BehatBundle\Event\TransitionEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CreateContentDraft extends AbstractProcessStage implements EventSubscriberInterface
{
    /** @var \EzSystems\BehatBundle\API\Facade\ContentFacade */
    private $contentFacade;

    /**
     * @var WorkflowFacade
     */
    private $workflowFacade;

    public static function getSubscribedEvents()
    {
        return [Events::START_TO_DRAFT => 'execute'];
    }

    protected function getTransitions(): array
    {
        return [
            Events::DRAFT_TO_REVIEW => 1,
        ];
    }

    public function __construct(EventDispatcher $eventDispatcher,
                                UserService $userService,
                                PermissionResolver $permissionResolver,
                                LoggerInterface $logger,
                                ContentFacade $contentFacade,
                                RandomDataGenerator $randomDataGenerator,
                                WorkflowFacade $workflowFacade)
    {
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

        // $transitionName = 'to_review';
        // $this->workflowFacade->transition($event->content, $transitionName, $this->randomDataGenerator->getRandomTextLine());
    }
}
