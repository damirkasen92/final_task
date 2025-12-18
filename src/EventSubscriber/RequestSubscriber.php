<?php

namespace App\EventSubscriber;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;

class RequestSubscriber implements EventSubscriberInterface
{
    public function __construct(private Security $security, private RouterInterface $router)
    {
    }

    public function onRequestEvent(RequestEvent $event): void
    {
        $user = $this->security->getUser();

        if ($user && method_exists($user, 'isBlocked') && $user->isBlocked()) {
            $loginUrl = $this->router->generate('login');
            $event->setResponse(new RedirectResponse($loginUrl));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onRequestEvent',
        ];
    }
}
