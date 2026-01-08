<?php
namespace App\Service\Registration;

use App\Dto\RegistrationDto;
use App\Entity\User;
use App\Exception\RegistrationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationService
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws RegistrationException
     */
    public function doRegistration(RegistrationDto $dto): void
    {
        if ($dto->repeatPassword !== $dto->password) {
            throw new RegistrationException($this->translator->trans('registration.repeat_passwords_error'));
        }

        $user = new User();
        $user->setName($dto->name);
        $user->setEmail($dto->email);
        $user->setPassword(
            $this->userPasswordHasher->hashPassword($user, $dto->password)
        );

        $this->entityManager->persist($user);

        try {
            $this->entityManager->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new RegistrationException(
                $this->translator->trans('registration.email_already_exists')
            );
        }
    }
}
