<?php
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;

class LocaleRedirectSubscriber implements EventSubscriberInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request       = $event->getRequest();
        $cookieLocale  = $request->cookies->get('user_locale');
        $currentLocale = $request->attributes->get('_locale');

        if ($cookieLocale && $cookieLocale !== $currentLocale) {
            $route  = $request->attributes->get('_route');
            $params = $request->attributes->get('_route_params', []);

            $params['_locale'] = $cookieLocale;

            $queryParams = $request->query->all();
            $params      = array_merge($params, $queryParams);

            $url = $this->router->generate($route, $params);

            $event->setResponse(new RedirectResponse($url));
        }
    }

    public static function getSubscribedEvents(): array
    {
        // it ruins debug bar and other dev mev things
        return [
            // RequestEvent::class => 'onKernelRequest',
        ];
    }
}
