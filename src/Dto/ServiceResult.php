<?php

namespace App\Dto;

class ServiceResult
{
    private string $status;
    private ?string $errors;

    public function setSuccess(): static
    {
        $this->status = 'ok';

        return $this;
    }

    public function setFail(array|string $errors): static
    {
        $this->status = 'fail';

        if (\is_array($errors)) {
            $this->errors = \implode('\n', $errors);
        }

        $this->errors = $errors;

        return $this;
    }

    public function isFaulty(): bool
    {
        return $this->status === 'fail';
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getErrors(): ?string
    {
        return $this->errors;
    }
}