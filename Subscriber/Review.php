<?php


namespace EzSystems\BehatBundle\Subscriber;


use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;
use EzSystems\BehatBundle\API\ContentData\ContentDataProvider;
use EzSystems\BehatBundle\Event\Events;
use EzSystems\BehatBundle\Event\TransitionEvent;
use EzSystems\EzPlatformWorkflow\Service\WorkflowService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Review extends AbstractProcessStage implements EventSubscriberInterface
{

    /** @var ContentDataProvider */
    private $contentDataProvider;
    /**
     * @var ContentService
     */
    private $contentService;
    /**
     * @var WorkflowService
     */
    private $workflowService;

    protected function getTransitions(): array
    {
        return [
            Events::REVIEW_TO_END => 0.1,
            Events::REVIEW_TO_REVIEW => 0.2,
            Events::REVIEW_TO_PUBLISH => 0.7,
        ];
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::DRAFT_TO_REVIEW => 'reviewDraft',
            Events::REVIEW_TO_REVIEW => 'reviewDraft',
        ];
    }

    public function __construct(EventDispatcher $eventDispatcher,
                                UserService $userService,
                                PermissionResolver $permissionResolver,
                                LoggerInterface $logger,
                                ContentDataProvider $contentDataProvider,
                                ContentService $contentService,
                                WorkflowService $workflowService)
    {
        parent::__construct($eventDispatcher, $userService, $permissionResolver, $logger);
        $this->contentDataProvider = $contentDataProvider;
        $this->contentService = $contentService;
        $this->workflowService = $workflowService;
    }

    public function reviewDraft(TransitionEvent $event)
    {
        $this->contentDataProvider->setContentTypeIdentifier($event->contentTypeIdentifier);
        $this->setCurrentUser($this->getRandomValue($event->editors));
        $contentUpdateStruct = $this->contentDataProvider->getRandomContentUpdateData($event->mainLanguage, $event->mainLanguage);

        $updatedDraft = $this->contentService->updateContent($event->content->getVersionInfo(), $contentUpdateStruct);
        $event->content = $updatedDraft;

        $workflowMetadata = $this->workflowService->loadWorkflowMetadataForContent($updatedDraft);
        $this->workflowService->apply($workflowMetadata, 're_review', 'Please have a look again');
        $this->transitionToNextStage($event);
    }
}