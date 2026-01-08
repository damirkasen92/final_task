<?php

namespace App\Security\Voter;

use App\Dto\InventoryIdsDto;
use App\Entity\Inventory;
use App\Entity\User;
use App\Enum\InventoryAttributes;
use App\Enum\UserRoles;
use App\Repository\InventoryRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class InventoryVoter extends Voter
{
    public function __construct(
        private InventoryRepository $inventoryRepository,
    ) {

    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return InventoryAttributes::tryFrom($attribute) !== null
            && ($subject instanceof InventoryIdsDto || $subject instanceof Inventory);
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @param Vote|null $vote
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        /** @var ?User $user */
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if (\in_array(UserRoles::ADMIN->value, $user->getRoles(), true)) {
            return true;
        }

        if ($subject instanceof InventoryIdsDto) {
            return $this->checkInventories($attribute, $subject, $user);
        }

        return $this->checkInventory($attribute, $subject, $user);
    }

    private function checkInventory(string $attribute, Inventory $subject, User $user)
    {
        if ($subject->getOwner() === $user) {
            return true;
        }

        return false;
    }

    private function checkInventories(string $attribute, InventoryIdsDto $subject, User $user): bool
    {
        $inventories = $this->inventoryRepository
            ->findBy(['id' => $subject->ids]);

        if ($this->belongsToUser($user, $inventories))
            return true;

        return false;
    }

    private function belongsToUser(User $user, array $inventories): bool
    {
        return array_all($inventories, fn($inventory) => $inventory->getOwner() === $user);
    }

    private function hasWriteAccess(User $user, array $inventories): bool
    {
        return array_all($inventories, fn($inventory) => $inventory->hasWriteAccess($user));

    }
}
