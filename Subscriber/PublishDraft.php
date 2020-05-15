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
use EzSystems\BehatBundle\API\ContentData\RandomDataGenerator;
use EzSystems\BehatBundle\Event\Events;
use EzSystems\BehatBundle\Event\TransitionEvent;
use EzSystems\EzPlatformWorkflow\Registry\WorkflowRegistryInterface;
use EzSystems\EzPlatformWorkflow\Service\WorkflowService;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PublishDraft extends AbstractProcessStage implements EventSubscriberInterface
{
    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    private $contentService;
    /**
     * @var WorkflowService
     */
    private $workflowService;
    /**
     * @var WorkflowRegistryInterface
     */
    private $workflowRegistry;
    /**
     * @var RandomDataGenerator
     */
    private $randomDataGenerator;

    protected function getTransitions(): array
    {
        return [
            Events::PUBLISH_TO_END => 0.7,
            Events::PUBLISH_TO_EDIT => 0.3,
        ];
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::REVIEW_TO_PUBLISH => 'publishDraft',
        ];
    }

    public function __construct(EventDispatcher $eventDispatcher,
                                UserService $userService,
                                PermissionResolver $permissionResolver,
                                LoggerInterface $logger,
                                ContentService $contentService,
                                WorkflowService $workflowService,
                                WorkflowRegistryInterface $workflowRegistry,
                                RandomDataGenerator $randomDataGenerator)
    {
        parent::__construct($eventDispatcher, $userService, $permissionResolver, $logger);
        $this->contentService = $contentService;
        $this->workflowService = $workflowService;
        $this->workflowRegistry = $workflowRegistry;
        $this->randomDataGenerator = $randomDataGenerator;
    }

    public function publishDraft(TransitionEvent $event)
    {
        try {
            $transitionName = 'done';
            $workflows = $this->workflowRegistry->getSupportedWorkflows($event->content);
            $workflow = array_shift($workflows);
            $workflowMetadata = $this->workflowService->loadWorkflowMetadataForContent($event->content, $workflow->getName());
            $this->workflowService->apply($workflowMetadata, $transitionName, $this->randomDataGenerator->getRandomTextLine());
            $event->content = $this->contentService->publishVersion($event->content->versionInfo);
        } catch (\Exception $ex) {
            $this->logger->log(LogLevel::ERROR, sprintf('Error occured during CreateContentDraft Stage: %s', $ex->getMessage()));
            return;
        }

        $this->transitionToNextStage($event);
    }
}
