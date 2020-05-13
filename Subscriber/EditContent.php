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
use EzSystems\BehatBundle\Event\Events;
use EzSystems\BehatBundle\Event\TransitionEvent;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EditContent extends AbstractProcessStage implements EventSubscriberInterface
{
    /**
     * @var ContentDataProvider
     */
    private $contentDataProvider;
    /**
     * @var ContentService
     */
    private $contentService;

    protected function getTransitions(): array
    {
        return [
            Events::EDIT_TO_END => 0.4,
            Events::EDIT_TO_PUBLISH => 0.5,
            Events::EDIT_TO_EDIT => 0.1,
        ];
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::EDIT_TO_EDIT => 'editContent',
            Events::PUBLISH_TO_EDIT => 'editContent',
        ];
    }

    public function __construct(LoggerInterface $logger, ContentDataProvider $contentDataProvider, ContentService $contentService, EventDispatcher $eventDispatcher, UserService $userService, PermissionResolver $permissionResolver)
    {
        parent::__construct($eventDispatcher, $userService, $permissionResolver, $logger);
        $this->contentDataProvider = $contentDataProvider;
        $this->contentService = $contentService;
    }

    public function editContent(TransitionEvent $event)
    {
        try {
            $this->contentDataProvider->setContentTypeIdentifier($event->contentTypeIdentifier);
            $this->setCurrentUser($this->getRandomValue($event->editors));
            $contentUpdateStruct = $this->contentDataProvider->getRandomContentUpdateData($event->mainLanguage, $this->getRandomValue($event->availableLanguages));

            $contentDraft = $this->contentService->createContentDraft($event->content->contentInfo);
            $updatedDraft = $this->contentService->updateContent($contentDraft->getVersionInfo(), $contentUpdateStruct);
            $event->content = $updatedDraft;

            $this->transitionToNextStage($event);
        } catch (\Exception $ex) {
            $this->logger->log(LogLevel::ERROR, sprintf('Error occured during EditContent Stage: %s', $ex->getMessage()));
        }
    }
}
