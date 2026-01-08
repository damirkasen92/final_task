<?php

namespace App\Service\FileStorage;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileStorageInterface
{
    public function upload(UploadedFile $file): string;
    public function getFileUrl(string $filename): string;
}
