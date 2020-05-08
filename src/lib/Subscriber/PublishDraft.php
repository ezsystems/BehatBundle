<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Subscriber;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;
use EzSystems\Behat\Event\TransitionEvent;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class PublishDraft extends AbstractProcessStage implements EventSubscriberInterface
{
    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    private $contentService;

    protected function getTransitions(): array
    {
        return [
            TransitionEvent::PUBLISH_TO_END => 0.3,
            TransitionEvent::PUBLISH_TO_EDIT => 0.7,
        ];
    }

    public static function getSubscribedEvents()
    {
        return [
            TransitionEvent::DRAFT_TO_PUBLISH => 'publishDraft',
            TransitionEvent::EDIT_TO_PUBLISH => 'publishDraft',
        ];
    }

    public function __construct(EventDispatcherInterface $eventDispatcher, UserService $userService, PermissionResolver $permissionResolver, LoggerInterface $logger, ContentService $contentService)
    {
        parent::__construct($eventDispatcher, $userService, $permissionResolver, $logger);
        $this->contentService = $contentService;
    }

    public function publishDraft(TransitionEvent $event)
    {
        try {
            $event->content = $this->contentService->publishVersion($event->content->versionInfo);
            $this->transitionToNextStage($event);
        } catch (Exception $ex) {
            $this->logger->log(LogLevel::ERROR, sprintf('Error occured during CreateContentDraft Stage: %s', $ex->getMessage()));
        }
    }
}
