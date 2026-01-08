<?php

namespace App\Service\Admin;

use App\Entity\User;
use App\Enum\UserRoles;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AdminService
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private Security $security,
        private TokenStorageInterface $tokenStorage,
        private RequestStack $requestStack,
    ) {
    }

    public function getAllUsers()
    {
        return $this->userRepository->getAllUsers();
    }

    public function blockUsers(array $userIds)
    {
        $users = $this->userRepository->findBy(['id' => $userIds]);

        foreach ($users as $user) {
            $user->setBlocked(true);
            $this->unauthorizeCurrentUser($user);
        }

        $this->entityManager->flush();
    }

    public function unblockUsers(array $userIds)
    {
        $users = $this->userRepository->findBy(['id' => $userIds]);

        foreach ($users as $user) {
            $user->setBlocked(false);
        }

        $this->entityManager->flush();
    }

    public function deleteUsers(array $userIds)
    {
        $users = $this->userRepository->findBy(['id' => $userIds]);

        foreach ($users as $user) {
            $this->entityManager->remove($user);
            $this->unauthorizeCurrentUser($user);
        }

        $this->entityManager->flush();
    }

    public function makeAdminUsers(array $userIds)
    {
        $users = $this->userRepository->findBy(['id' => $userIds]);

        foreach ($users as $user) {
            $this->setRole($user, UserRoles::ADMIN);
        }

        $this->entityManager->flush();
    }

    public function unmakeAdminUsers(array $userIds)
    {
        $users = $this->userRepository->findBy(['id' => $userIds]);

        foreach ($users as $user) {
            $this->removeRole($user, UserRoles::ADMIN);
            $this->unauthorizeCurrentUser($user);
        }

        $this->entityManager->flush();
    }

    private function setRole(User $user, UserRoles $role)
    {
        $roles = $user->getRoles();

        if (!\in_array($role->value, $roles)) {
            $roles[] = $role->value;
            $user->setRoles($roles);
        }
    }

    private function removeRole(User $user, UserRoles $role)
    {
        $roles = $user->getRoles();
        $idx = array_search($role->value, $roles); // O(n)
        array_splice($roles, $idx, 1); // O(n) it can be done with one loop, but it will take more memory
        $user->setRoles($roles);
    }

    private function unauthorizeCurrentUser(User $user)
    {
        $currentUser = $this->security->getUser();

        if ($currentUser === $user) {
            $this->tokenStorage->setToken(null);
            $this->requestStack->getSession()->invalidate();
        }
    }
}
