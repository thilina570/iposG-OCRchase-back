<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class FileUploadService
{
    public function storeFile(
        UploadedFile $file,
        string $directory = 'uploads',
        ?string $disk = 'public'
    ): string {
        try {
            // Generate a random file name to avoid collisions
            $filename = $this->generateFileName($file->getClientOriginalExtension());

            // Construct final storage path
            $path = rtrim($directory, '/') . '/' . $filename;

            // Store the file
            $storedPath = $file->storeAs($directory, $filename, $disk);

            return $storedPath; // e.g., "uploads/random-name.jpg"
        } catch (Exception $e) {
            // Handle any storage errors as needed
            throw new Exception('Error uploading file: ' . $e->getMessage());
        }
    }

    protected function generateFileName(?string $extension): string
    {
        $uuid = Str::uuid()->toString();
        return $uuid . '.' . $extension;
    }
}
