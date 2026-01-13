<?php

namespace App\Service\Post;

use App\Entity\Inventory;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class PostService
{
    public function __construct(private EntityManagerInterface $em, private HubInterface $hub)
    {
    }

    public function createPost(Post $post, Inventory $inventory): void
    {
        $post->setInventory($inventory);
        $this->em->persist($post);
        $this->em->flush();
        $this->publishCreatePost($post);
    }

    public function publishCreatePost(Post $post): void
    {
        $update = new Update(
            '/posts',
            json_encode(
                [
                    'id' => $post->getId(),
                    'message' => $post->getMessage(),
                    'user' => $post->getCreatedBy(),
                    'time' => $post->getCreatedAt()->format('D-M-Y H:i:s'),
                ]
            )
        );

        $this->hub->publish($update);
    }
}
