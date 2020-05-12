<?php


namespace EzSystems\BehatBundle\Subscriber;


use EzSystems\BehatBundle\Event\TransitionEvent;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;

class InitialStage extends AbstractProcessStage implements EventSubscriberInterface
{

    protected function getTransitions(): array
    {
        return [
//            TransitionEvent::
        ];
    }

    public static function getSubscribedEvents()
    {
        return [
//            Events::START => 'onStart',
        ];
    }
}