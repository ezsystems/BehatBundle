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
use EzSystems\BehatBundle\API\Facade\SearchFacade;
use EzSystems\BehatBundle\Event\TransitionEvent;
use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CreateContentDraft extends AbstractProcessStage implements EventSubscriberInterface
{
    /** @var \EzSystems\BehatBundle\API\Facade\ContentFacade */
    private $contentFacade;

    /** @var \EzSystems\BehatBundle\API\Facade\SearchFacade */
    private $searchFacade;

    public static function getSubscribedEvents()
    {
        return [TransitionEvent::START => 'onProcessStarted'];
    }

    protected function getTransitions(): array
    {
        return [
            TransitionEvent::DRAFT_TO_END => 0.1,
            TransitionEvent::DRAFT_TO_PUBLISH => 0.9,
        ];
    }

    public function __construct(EventDispatcher $eventDispatcher,
                                UserService $userService,
                                PermissionResolver $permissionResolver,
                                LoggerInterface $logger,
                                ContentFacade $contentFacade,
                                SearchFacade $searchFacade)
    {
        parent::__construct($eventDispatcher, $userService, $permissionResolver, $logger);
        $this->contentFacade = $contentFacade;
        $this->searchFacade = $searchFacade;
    }

    public function onProcessStarted(TransitionEvent $eventData): void
    {
        $this->setCurrentUser($eventData->userName);

        try {
            $parentPath = $this->searchFacade->getRandomChildFromPath($eventData->parentPath);
            $content = $this->contentFacade->createContentDraft($eventData->contentTypeIdentifier, $parentPath, $eventData->language);
            $eventData->content = $content;
            $this->transitionToNextStage($eventData);
        } catch (Exception $ex) {
            $this->logger->log(LogLevel::ERROR, sprintf('Error occured during CreateContentDraft Stage: %s', $ex->getMessage()));
        }
    }
}
