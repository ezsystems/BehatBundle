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
use EzSystems\BehatBundle\Event\Events;
use EzSystems\BehatBundle\Event\TransitionEvent;
use EzSystems\EzPlatformWorkflow\Registry\WorkflowRegistryInterface;
use EzSystems\EzPlatformWorkflow\Service\WorkflowServiceInterface;
use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CreateContentDraft extends AbstractProcessStage implements EventSubscriberInterface
{
    /** @var \EzSystems\BehatBundle\API\Facade\ContentFacade */
    private $contentFacade;

    /** @var WorkflowServiceInterface */
    private $workflowService;

    /** @var WorkflowRegistryInterface */
    private $workflowRegistry;
    /** @var RandomDataGenerator */
    private $randomDataGenerator;

    public static function getSubscribedEvents()
    {
        return [Events::START_TO_DRAFT => 'createDraft'];
    }

    protected function getTransitions(): array
    {
        return [
            Events::DRAFT_TO_END => 0.1,
            Events::DRAFT_TO_REVIEW => 0.9,
        ];
    }

    public function __construct(EventDispatcher $eventDispatcher,
                                UserService $userService,
                                PermissionResolver $permissionResolver,
                                LoggerInterface $logger,
                                ContentFacade $contentFacade,
                                WorkflowServiceInterface $workflowService,
                                WorkflowRegistryInterface $workflowRegistry,
                                RandomDataGenerator $randomDataGenerator)
    {
        parent::__construct($eventDispatcher, $userService, $permissionResolver, $logger);
        $this->contentFacade = $contentFacade;
        $this->workflowService = $workflowService;
        $this->workflowRegistry = $workflowRegistry;
        $this->randomDataGenerator = $randomDataGenerator;
    }

    public function createDraft(TransitionEvent $event): void
    {
        $event->author = $this->getRandomValue($event->editors);
        $this->setCurrentUser($event->author);
        $this->randomDataGenerator->setLanguage($event->mainLanguage);

        try {
            $contentDraft = $this->contentFacade->createContentDraft($event->contentTypeIdentifier, $event->locationPath, $event->mainLanguage);
            $event->content = $contentDraft;

            $transitionName = 'to_review';
            $workflows = $this->workflowRegistry->getSupportedWorkflows($contentDraft);
            $workflow = array_shift($workflows);
            $workflowMetadata = $this->workflowService->loadWorkflowMetadataForContent($contentDraft, $workflow->getName());
            $this->workflowService->apply($workflowMetadata, $transitionName, $this->randomDataGenerator->getRandomTextLine());
        } catch (\Exception $ex) {
            $this->logger->log(LogLevel::ERROR, sprintf('Error occured during CreateContentDraft Stage: %s', $ex->getMessage()));
            return;
        }
        $this->transitionToNextStage($event);
    }
}
