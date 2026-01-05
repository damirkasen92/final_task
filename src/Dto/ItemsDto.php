<?php
namespace App\Dto;

use Knp\Component\Pager\Pagination\PaginationInterface;

readonly class ItemsDto
{
    public function __construct(
        public PaginationInterface $pagination,
        public array $itemSlots,
        public array $itemFieldNames
    ) {
    }
}
