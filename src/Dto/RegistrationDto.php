<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

readonly class RegistrationDto
{
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    public string $name;

    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 8, max: 16)]
    public string $password;

    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 8, max: 16)]
    public string $repeatPassword;

    public static function fromRequest(Request $request): self
    {
        $dto = new self();
        $dto->name = $request->request->get('name');
        $dto->email = $request->request->get('email');
        $dto->password = $request->request->get('password');
        $dto->repeatPassword = $request->request->get('repeat_password');

        return $dto;
    }
}
