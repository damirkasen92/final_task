<?php

namespace App\Controller;

use App\Dto\RegistrationDto;
use App\Exception\RegistrationException;
use App\Service\Registration\RegistrationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(path: [
    'en' => '/',
    'ru' => '/ru',
])]
class RegistrationController extends BaseController
{
    public function __construct(
        private readonly RegistrationService $registrationService,
        private readonly ValidatorInterface $validator,
    )
    {
    }

    #[Route(path: '/registration', name: 'show_registration', methods: ['GET'])]
    public function show(): Response
    {
        return $this->render('security/registration.html.twig');
    }

    #[Route(path: '/registration', name: 'registration', methods: ['POST'])]
    public function registration(Request $request): Response
    {
        $dto = RegistrationDto::fromRequest($request);
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getPropertyPath() . ': ' .  $error->getMessage());
            }
            return $this->redirectToRoute('show_registration');
        }

        try {
            $this->registrationService->doRegistration($dto);
        } catch (RegistrationException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('show_registration');
        }

        return $this->redirect('login');
    }
}
