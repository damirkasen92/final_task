<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/user/{id}', name: 'show_user', methods: ['GET'])]
    public function index(User $user): Response
    {
        $this->denyAccessUnlessGranted('SHOW_USER', $this->getUser());

        return $this->render('user/index.html.twig', [
            'form' => $this->createForm(UserProfileType::class, $user),
            'currentUser' => $user
        ]);
    }

    #[Route('/user/{id}/edit', name: 'edit_user', methods: ['POST'])]
    public function editUser(Request $request, User $user, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('EDIT_USER', $this->getUser());

        //TODO send confirm message to the new email
        $form = $this->createForm(UserProfileType::class, $user)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
        }

        foreach ($form->getErrors(true) as $error) {
            $this->addFlash('error', $error->getMessage());   
        }

        return $this->redirectToRoute('show_user', [
            'id' => $user->getId(),
        ]);
    }
}
