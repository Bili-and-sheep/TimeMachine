<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploadService
{
    public function __construct(
        private string $uploadDir,
    ) {}

    public function upload(UploadedFile $file): string
    {
        $filename = bin2hex(random_bytes(16)) . '.' . $file->guessExtension();
        $file->move($this->uploadDir, $filename);

        return '/uploads/products/' . $filename;
    }

    public function delete(string $path): void
    {
        $fullPath = $this->uploadDir . '/' . basename($path);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}