<?php
namespace App\Form\DataTransformer;

use App\Service\FileStorage\FileStorageInterface;
use Symfony\Component\Form\DataTransformerInterface;

class FileToStringTransformer implements DataTransformerInterface
{
    private ?string $fileUrl = null;

    public function __construct(
        private FileStorageInterface $fileStorage
    ) {

    }

    public function transform(mixed $value): mixed
    {
        if ($value) {
            $this->fileUrl = $value;
        }

        return null;
    }

    public function reverseTransform(mixed $value): mixed
    {
        if (!$value) {
            return $this->fileUrl;
        }

        if (\is_string($value)) {
            return $value;
        }

        return $this->fileStorage->upload($value);
    }
}
