<?php


namespace EzSystems\BehatBundle\Subscriber;


use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\Router;
use EzSystems\BehatBundle\API\ContentData\RandomDataGenerator;
use EzSystems\BehatBundle\API\Facade\WorkflowFacade;
use EzSystems\BehatBundle\Event\Events;
use EzSystems\BehatBundle\Event\TransitionEvent;
use EzSystems\DateBasedPublisher\API\Repository\DateBasedPublisherServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PublishInTheFuture extends AbstractProcessStage implements EventSubscriberInterface
{
    /**
     * @var DateBasedPublisherServiceInterface
     */
    private $dateBasedPublisherService;
    /**
     * @var WorkflowFacade
     */
    private $workflowFacade;

    public function __construct(EventDispatcher $eventDispatcher,
                                UserService $userService,
                                PermissionResolver $permissionResolver,
                                LoggerInterface $logger,
                                RandomDataGenerator $randomDataGenerator,
                                DateBasedPublisherServiceInterface $dateBasedPublisherService,
                                WorkflowFacade $workflowFacade)
    {
        parent::__construct($eventDispatcher, $userService, $permissionResolver, $logger, $randomDataGenerator);

        $this->dateBasedPublisherService = $dateBasedPublisherService;
        $this->workflowFacade = $workflowFacade;
    }


    protected function getTransitions(): array
    {
        return [
            Events::PUBLISH_LATER_TO_END => 1,
        ];
    }


    protected function doExecute(TransitionEvent $event): void
    {
        $transitionName = 'done';
        $this->workflowFacade->transition($event->content, $transitionName, $this->randomDataGenerator->getRandomTextLine());

        $urlRoot = 'http://v2.local/admin';
        $this->dateBasedPublisherService->scheduleVersion($event->content->versionInfo, $this->randomDataGenerator->getRandomDateInTheFuture(), $urlRoot);
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::REVIEW_TO_PUBLISH_LATER => 'execute',
        ];
    }
}