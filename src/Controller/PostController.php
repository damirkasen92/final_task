<?php

namespace App\Controller;

use App\Entity\Inventory;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Service\Post\PostService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PostController extends BaseController
{
    #[Route('/inventory/{id}/discussion', name: 'show_discussion')]
    public function showDiscussion(Inventory $inventory, PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'inventory' => $inventory,
            'posts' => $postRepository->findBy([
                'inventory' => $inventory,
            ]),
        ]);
    }

    #[Route('/inventory/{id}/post/create', name: 'create_post', methods: ['POST'])]
    public function createPost(Request $request, Inventory $inventory, PostService $postService): Response
    {
        $form = $this->createForm(PostType::class)
            ->submit($request->request->all(), false);

        if ($form->isValid()) {
            $post = $form->getData();
            $postService->createPost($post, $inventory);

            return $this->json($this->jsonSuccessData, Response::HTTP_CREATED);
        }

        return $this->json($this->jsonErrorData, Response::HTTP_BAD_REQUEST);
    }

}
