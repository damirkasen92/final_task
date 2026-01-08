<?php
namespace App\Form\DataTransformer;

use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class TagsTransformer implements DataTransformerInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function transform($tags): mixed
    {
        return $tags;
    }

    public function reverseTransform($string): array
    {
        if (!$string) {
            return [];
        }

        $ids = array_map(
            'trim',
            explode(',', $string)
        );
        return $this->em->getRepository(Tag::class)
            ->findBy(['id' => $ids]);
    }
}
