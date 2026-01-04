<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

readonly class InventoryIdsDto
{
    #[Assert\NotBlank]
    #[Assert\All([
        new Assert\Type('digit'),
        new Assert\Positive(),
    ])]
    public array $ids;

    public static function fromRequest(Request $request): self
    {
        $dto = new self();
        $dto->ids = $request->request->all('inventoryIds');

        return $dto;
    }
}
