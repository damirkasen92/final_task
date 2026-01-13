<?php

namespace App\EventListener;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsEntityListener(event: Events::prePersist, entity: Post::class)]
class PostListener
{
    public function __construct(private Security $security)
    {
    }

    public function prePersist(Post $post, PrePersistEventArgs $event)
    {
        $post->setCreatedAt(new \DateTimeImmutable());
        $post->setCreatedBy(
            $this->security->getUser()
        );
    }
}
