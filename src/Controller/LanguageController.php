<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;

class LanguageController extends BaseController
{
    #[Route('/change/{locale}', name: 'change_locale')]
    public function changeLanguage(string $locale, RouterInterface $router, Request $request): RedirectResponse
    {
        $url = $router->generate(
            $request->query->get('route'),
            \array_merge(
                $request->query->all('query'),
                $request->query->all('params'),
                ['_locale' => $locale],
            )
        );

        $response = $this->redirect($url);

        $response->headers->setCookie(
            new Cookie(
                'user_locale',
                $locale,
                strtotime('+1 year'),
                secure: false,
                httpOnly: false,
                raw: false
            )
        );

        return $response;
    }
}
