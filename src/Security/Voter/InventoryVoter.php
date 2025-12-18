<?php

namespace App\Security\Voter;

use App\Entity\Inventory;
use App\Enum\InventoryAttributes;
use App\Enum\UserRoles;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class InventoryVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return InventoryAttributes::tryFrom($attribute) !== null
            && $subject instanceof \App\Entity\Inventory;
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
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if (
            $subject->getOwner() === $user
            || in_array(UserRoles::ADMIN->value, $user->getRoles(), true)
        ) {
            return true;
        }

        switch ($attribute) {
            case InventoryAttributes::TAB_ITEMS->value:
            case InventoryAttributes::TAB_DISCUSSION->value:
                return $subject->hasWriteAccess($user);
        }

        return false;
    }
}
