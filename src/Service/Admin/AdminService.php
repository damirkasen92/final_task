<?php

namespace App\Service\Admin;

use App\Enum\UserRoles;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class AdminService
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    )
    {
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
        }

        $this->entityManager->flush();
    }

    public function makeAdminUsers(array $userIds)
    {
        $users = $this->userRepository->findBy(['id' => $userIds]);

        foreach ($users as $user) {
            $roles = $user->getRoles();

            if (!in_array(UserRoles::ADMIN->value, $roles)) {
                $roles[] = UserRoles::ADMIN->value;
                $user->setRoles($roles);
            }
        }

        $this->entityManager->flush();
    }

    public function unmakeAdminUsers(array $userIds)
    {
        $users = $this->userRepository->findBy(['id' => $userIds]);

        foreach ($users as $user) {
            $roles = $user->getRoles();
            $idx = array_search(UserRoles::ADMIN->value, $roles); //O(n)
            array_splice($roles, $idx, 1); // O(n) it can be done with one loop, but it will take more memory
            $user->setRoles($roles);
        }

        $this->entityManager->flush();
    }
}
