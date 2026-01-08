<?php

namespace App\EventSubscriber;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RequestSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security,
        private RouterInterface $router,
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    public function onRequestEvent(RequestEvent $event): void
    {
        /** @var ?User $user */
        $user = $this->security->getUser();

        if (
            $user?->isBlocked()
            && 'login' !== $event->getRequest()->attributes->get('_route')
        ) {
            $this->unauthorizeCurrentUser($event);
            $event->setResponse(
                new RedirectResponse($this->router->generate('login'))
            );
        }
    }

    private function unauthorizeCurrentUser(RequestEvent $event): void
    {
        $this->tokenStorage->setToken(null);
        $event->getRequest()->getSession()->invalidate();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onRequestEvent',
        ];
    }
}
