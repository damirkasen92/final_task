<?php

namespace App\Dto;

use App\Entity\User;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;

class ItemDto
{
    public ?int $inventoryId;
    public ?\DateTimeImmutable $createdAt = null;
    public ?User $createdBy = null;

    public static function fromRequest(Request $request): static
    {
        $dto = new self();
        $dto->inventoryId = $request->request->getInt('inventoryId');
        $dto->createdAt = new DateTimeImmutable($request->request->getString('createdAt'));
        $dto->createdBy = $request->getUser();

        return $dto;
    }
}
