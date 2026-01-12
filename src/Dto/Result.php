<?php
namespace App\Dto;

class Result
{
    public function __construct(
        private string $status,
        private ?string $message = null,
        private ?int $version = null
    ) {
    }

    public static function ok(): self
    {
        return new self('ok');
    }

    public static function conflict(string $message): self
    {
        return new self('conflict', $message);
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
