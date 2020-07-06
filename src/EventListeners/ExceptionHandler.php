<?php

namespace App\EventListeners;

use App\Helper\ResponseFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionHandler implements EventSubscriberInterface
{
    public static function getSubscribedEvents(
    ) {
        return [
            KernelEvents::EXCEPTION => 'handleExceptions'
        ];
    }

    public function handleExceptions(ExceptionEvent $event)
    {
        $response = ResponseFactory::fromError(
            $event->getThrowable()
        );

        $event->setResponse($response->getResponse());
    }
}