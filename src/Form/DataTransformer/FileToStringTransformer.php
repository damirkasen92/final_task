<?php
namespace App\Form\DataTransformer;

use App\Service\Google\GoogleStorageService;
use Symfony\Component\Form\DataTransformerInterface;

class FileToStringTransformer implements DataTransformerInterface
{
    private ?string $fileUrl = null;

    public function __construct(
        private GoogleStorageService $googleStorageService
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

        return $this->googleStorageService->upload($value);
    }
}
