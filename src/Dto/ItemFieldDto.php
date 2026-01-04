<?php

namespace App\Dto;

use App\Enum\ItemFieldTypes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

readonly class ItemFieldDto
{
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    public ?string $title;

    #[Assert\Type('string')]
    public ?string $description;

    #[Assert\Type('bool')]
    public ?bool $isDisplayed;

    public ?ItemFieldTypes $type;

    #[Assert\Type('int')]
    public int $orderIndex;

    #[Assert\Type('int')]
    public int $inventoryId;

    public static function fromRequest(Request $request): static
    {
        $dto = new self();
        $dto->title = $request->request->getString('title');
        $dto->description = $request->request->getString('description');
        $dto->isDisplayed = $request->request->getBoolean('isDisplayed', false);
        $dto->type = $request->request->getEnum('type', ItemFieldTypes::class);
        $dto->orderIndex = $request->request->getInt('orderIndex');
        $dto->inventoryId = $request->request->getInt('inventoryId');

        return $dto;
    }
}
