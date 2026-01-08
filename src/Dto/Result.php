<?php
namespace App\Dto;

class Result
{
    public function __construct(
        private string $status,
        private ?string $message = null,
        private null | string | array $dbData = null,
        private null | string | array $formData = null,
        private ?int $version = null
    ) {
    }

    public static function ok(): self
    {
        return new self('ok');
    }

    public static function conflict(string $message, string | array $dbData, string | array $formData): self
    {
        return new self('conflict', $message, $dbData, $formData);
    }

    public function getDbData(): array | string | null
    {
        return $this->dbData;
    }

    public function getFormData(): array | string | null
    {
        return $this->formData;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }
}
