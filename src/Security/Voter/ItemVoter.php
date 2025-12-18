<?php

namespace App\Security\Voter;

use App\Enum\ItemAttributes;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class ItemVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return ItemAttributes::tryFrom($attribute) !== null
            && $subject instanceof \App\Entity\Item;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();
        $isAuthenticatedUser = $user instanceof UserInterface;

        if (
            $isAuthenticatedUser
            && in_array('ROLE_ADMIN', $user->getRoles(), true)
        ) {
            return true;
        }

        $item = $subject;
        $inventory = $item->getInventory();

        switch ($attribute) {
            case ItemAttributes::ADD->value:
            case ItemAttributes::EDIT->value:
            case ItemAttributes::DELETE->value:
                if (
                    $isAuthenticatedUser
                    && ($inventory->getOwner() === $user
                        || $inventory->hasWriteAccess($user)
                        || $inventory->isPublic())
                ) return true;

                return false;

            case ItemAttributes::VIEW->value:
                return true;
        }

        return false;
    }
}
