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
use EzSystems\BehatBundle\API\ContentData\RandomDataGenerator;
use EzSystems\BehatBundle\Event\Events;
use EzSystems\BehatBundle\Event\TransitionEvent;
use EzSystems\EzPlatformWorkflow\Registry\WorkflowRegistryInterface;
use EzSystems\EzPlatformWorkflow\Service\WorkflowService;
use EzSystems\EzPlatformWorkflow\Service\WorkflowServiceInterface;
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
    /**
     * @var WorkflowServiceInterface
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
            Events::EDIT_TO_END => 0.1,
            Events::EDIT_TO_REVIEW => 0.9,
        ];
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::PUBLISH_TO_EDIT => 'editContent',
        ];
    }

    public function __construct(LoggerInterface $logger,
                                ContentDataProvider $contentDataProvider,
                                ContentService $contentService,
                                EventDispatcher $eventDispatcher,
                                UserService $userService,
                                PermissionResolver $permissionResolver,
                                WorkflowServiceInterface $workflowService,
                                WorkflowRegistryInterface $workflowRegistry,
                                RandomDataGenerator $randomDataGenerator)
    {
        parent::__construct($eventDispatcher, $userService, $permissionResolver, $logger);
        $this->contentDataProvider = $contentDataProvider;
        $this->contentService = $contentService;
        $this->workflowService = $workflowService;
        $this->workflowRegistry = $workflowRegistry;
        $this->randomDataGenerator = $randomDataGenerator;
    }

    public function editContent(TransitionEvent $event)
    {
        try {
            $this->contentDataProvider->setContentTypeIdentifier($event->contentTypeIdentifier);
            $this->setCurrentUser($this->getRandomValue($event->editors));
            $language = $this->getRandomValue($event->availableLanguages);
            $this->randomDataGenerator->setLanguage($language);

            $contentDraft = $this->contentService->createContentDraft($event->content->contentInfo);
            $contentUpdateStruct = $this->contentDataProvider->getRandomContentUpdateData($event->mainLanguage, $language);
            $updatedDraft = $this->contentService->updateContent($contentDraft->getVersionInfo(), $contentUpdateStruct);

            $event->content = $updatedDraft;
            $transitionName = 'to_review';
            $workflows = $this->workflowRegistry->getSupportedWorkflows($updatedDraft);
            $workflow = array_shift($workflows);
            $workflowMetadata = $this->workflowService->loadWorkflowMetadataForContent($updatedDraft, $workflow->getName());
            $this->workflowService->apply($workflowMetadata, $transitionName, $this->randomDataGenerator->getRandomTextLine());

            $this->transitionToNextStage($event);
        } catch (\Exception $ex) {
            $this->logger->log(LogLevel::ERROR, sprintf('Error occured during EditContent Stage: %s', $ex->getMessage()));
        }
    }
}
