<?php


namespace EzSystems\BehatBundle\Subscriber;

use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;
use EzSystems\BehatBundle\API\Facade\SearchFacade;
use EzSystems\BehatBundle\Event\Events;
use EzSystems\BehatBundle\Event\InitialEvent;
use EzSystems\BehatBundle\Event\TransitionEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InitialStage extends AbstractProcessStage implements EventSubscriberInterface
{
    /**  @var \EzSystems\BehatBundle\API\Facade\SearchFacade */
    private $searchFacade;

    protected function getTransitions(): array
    {
        return [
            Events::START_TO_DRAFT => 1,
        ];
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::START => 'onStart',
        ];
    }

    public function __construct(EventDispatcher $eventDispatcher, UserService $userService, PermissionResolver $permissionResolver, LoggerInterface $logger, SearchFacade $searchFacade)
    {
        parent::__construct($eventDispatcher, $userService, $permissionResolver, $logger);
        $this->searchFacade = $searchFacade;
    }

    public function onStart(InitialEvent $event)
    {
        $contentTypeIdentifier = $this->getContentType($event->contentTypes);
        $locationPath = $this->getLocationPath($event->subtreePath);

        $transitionEvent = new TransitionEvent($event->editors, $contentTypeIdentifier, $locationPath, $event->languages, $event->mainLanguage);

        $this->transitionToNextStage($transitionEvent);
    }

    private function getContentType($contentTypesData): string
    {
        $randomNumber = $this->getRandomNumber();
        $threshold = 0;
        foreach ($contentTypesData as $contentTypeData) {
            $contentTypeIdentifier = array_key_first($contentTypeData);
            $probability = $contentTypeData[$contentTypeIdentifier]['probability'];
            if ($threshold <= $randomNumber && $randomNumber < $threshold + $probability) {
                return $contentTypeIdentifier;
            }

            $threshold += $probability;
        }

        throw new \Exception('Could not determine contentTypeIdentifier.');
    }

    private function getLocationPath(string $subtreePath): string
    {
        return $this->searchFacade->getRandomChildFromPath($subtreePath);
    }
}