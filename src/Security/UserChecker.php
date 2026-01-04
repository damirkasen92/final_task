<?php

namespace App\Security;

use App\Entity\User;
use App\Exception\LoginException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserChecker implements UserCheckerInterface
{
    public function __construct(
        private TranslatorInterface $translator
    ) {
    }

    public function checkPreAuth(UserInterface|User $user): void
    {
        if ($user->isBlocked()) {
            throw new LoginException(
                $this->translator->trans('security.user_checker.blocked')
            );
        }
    }

    public function checkPostAuth(UserInterface $user, ?TokenInterface $token = null): void
    {

    }
}
