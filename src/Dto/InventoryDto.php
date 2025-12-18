<?php

namespace App\Dto;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

readonly class InventoryDto
{
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    public string $title;

    #[Assert\Type('string')]
    public ?string $description;

    #[Assert\Type('string')]
    #[Assert\NotBlank]
    public string $category;

    #[Assert\Type('bool')]
    #[Assert\NotBlank]
    public bool $isPublic;

    #[Assert\Type('string')]
    public ?string $imageUrl;

    #[Assert\Type('array')]
    public array $tags;

    public static function fromRequest(Request $request): self
    {
        $dto = new self();
        $dto->title = $request->request->get('title');
        $dto->description = $request->request->get('description', null);
        $dto->category = $request->request->get('category');
        $dto->isPublic = $request->request->get('isPublic');
        $dto->imageUrl = $request->request->get('imageUrl');
        $dto->tags = $request->request->all('tags');

        return $dto;
    }
}
