<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

trait FileTrait
{
    public function uploadImage($file, $path): string|null
    {
        if (!$file) {
            return null;
        }

        try {
            $imageName = $file->hashName();
            $filename = pathinfo($imageName, PATHINFO_FILENAME);
            $imageName = $filename . '.webp';

            $imageManager = new ImageManager(new Driver());
            $image = $imageManager->read($file)
                ->toWebp(quality: 70, strip: true);

            $fullPath = $path . $imageName;

            // Upload to S3
            $uploaded = Storage::disk('s3')
                ->put($fullPath, $image, [
                    // 'visibility' => 'public',
                    'ContentType' => 'image/webp'
                ]);

            if (!$uploaded) {
                throw new FileException('Failed to upload image to S3');
            }

            return $fullPath;

        } catch (FileException $e) {
            Log::error('Image upload failed: ' . $e->getMessage(), [
                'file' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType()
            ]);

            return null;
        }
    }

    public function removeImage($path): void
    {
        if (!$path) {
            return;
        }
        Storage::disk('s3')->delete($path);
    }

}
