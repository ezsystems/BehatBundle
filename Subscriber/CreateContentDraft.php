<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\BehatBundle\Subscriber;

use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;
use EzSystems\BehatBundle\API\Facade\ContentFacade;
use EzSystems\BehatBundle\Event\Events;
use EzSystems\BehatBundle\Event\TransitionEvent;
use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CreateContentDraft extends AbstractProcessStage implements EventSubscriberInterface
{
    /** @var \EzSystems\BehatBundle\API\Facade\ContentFacade */
    private $contentFacade;

    public static function getSubscribedEvents()
    {
        return [Events::START_TO_DRAFT => 'createDraft'];
    }

    protected function getTransitions(): array
    {
        return [
            Events::DRAFT_TO_END => 0.1,
            Events::DRAFT_TO_PUBLISH => 0.9,
        ];
    }

    public function __construct(EventDispatcher $eventDispatcher,
                                UserService $userService,
                                PermissionResolver $permissionResolver,
                                LoggerInterface $logger,
                                ContentFacade $contentFacade)
    {
        parent::__construct($eventDispatcher, $userService, $permissionResolver, $logger);
        $this->contentFacade = $contentFacade;
    }

    public function createDraft(TransitionEvent $event): void
    {
        $event->author = $this->getRandomValue($event->editors);
        $this->setCurrentUser($event->author);

        try {
            $content = $this->contentFacade->createContentDraft($event->contentTypeIdentifier, $event->locationPath, $event->mainLanguage);
            $event->content = $content;
            $this->transitionToNextStage($event);
        } catch (\Exception $ex) {
            $this->logger->log(LogLevel::ERROR, sprintf('Error occured during CreateContentDraft Stage: %s', $ex->getMessage()));
        }
    }
}
