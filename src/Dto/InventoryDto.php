<?php

namespace App\Dto;

use App\Entity\Category;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormInterface;

readonly class InventoryDto
{
    public string $title;

    public ?string $description;

    public Category $category;

    public ?bool $isPublic;

    public ?string $imageUrl;

    public ?ArrayCollection $writers;

    public ?ArrayCollection $tags;

    public ?int $version;

    public static function fromForm(FormInterface $form): static
    {
        $dto = new self();
        $dto->title = $form->get('title')->getData();
        $dto->description = $form->get('description')->getData();
        $dto->category = $form->get('category')->getData();
        $dto->isPublic = $form->get('isPublic')->getData();
        $dto->imageUrl = $form->get('imageUrl')->getData();
        $dto->writers = $form->get('writers')->getData();
        $dto->tags = $form->get('tags')->getData();

        return $dto;
    }
}
