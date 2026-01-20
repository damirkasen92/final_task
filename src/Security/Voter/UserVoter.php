<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Enum\UserRoles;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;

final class UserVoter extends Voter
{
    public const SHOW_USER = 'SHOW_USER';
    public const EDIT_USER = 'EDIT_USER';
    public const CREATE_SALESFORCE_ACCOUNT = 'CREATE_SALESFORCE_ACCOUNT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // simple implementation
        return \in_array($attribute, [self::CREATE_SALESFORCE_ACCOUNT, self::SHOW_USER, self::EDIT_USER])
            && $subject instanceof User;
    }

    /**
     * @param string $attribute
     * @param User $subject
     * @param TokenInterface $token
     * @param mixed $vote
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        /**
         * @var User $user
         */
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::CREATE_SALESFORCE_ACCOUNT:
            case self::SHOW_USER:
            case self::EDIT_USER:
                if (
                    \in_array(UserRoles::ADMIN->value, $user->getRoles(), true)
                    || $subject->getId() === $user->getId()
                ) {
                    return true;
                }

                break;
        }

        return false;
    }
}
