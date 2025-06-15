<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait ImageUploadTrait
{
    /**
     * Upload and optimize image
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param int $width
     * @param int $height
     * @param int $quality
     * @return string
     */
    public function uploadImage(UploadedFile $file, $folder = 'images', $width = 800, $height = 600, $quality = 85)
    {
        $filename = $this->generateImageFilename($file);
        $path = $folder . '/' . $filename;

        // Resize and optimize image
        $image = Image::make($file)
            ->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode('jpg', $quality);

        Storage::disk('public')->put($path, $image->stream());

        return $path;
    }

    /**
     * Upload image without resizing
     *
     * @param UploadedFile $file
     * @param string $folder
     * @return string
     */
    public function uploadImageOriginal(UploadedFile $file, $folder = 'images')
    {
        $filename = $this->generateImageFilename($file);
        $path = $folder . '/' . $filename;

        Storage::disk('public')->putFileAs($folder, $file, $filename);

        return $path;
    }

    /**
     * Upload multiple images
     *
     * @param array $files
     * @param string $folder
     * @param int $width
     * @param int $height
     * @param int $quality
     * @return array
     */
    public function uploadMultipleImages(array $files, $folder = 'images', $width = 800, $height = 600, $quality = 85)
    {
        $uploadedPaths = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $uploadedPaths[] = $this->uploadImage($file, $folder, $width, $height, $quality);
            }
        }

        return $uploadedPaths;
    }

    /**
     * Delete image from storage
     *
     * @param string $path
     * @return bool
     */
    public function deleteImage($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }

    /**
     * Delete multiple images
     *
     * @param array $paths
     * @return int Number of deleted images
     */
    public function deleteMultipleImages(array $paths)
    {
        $deletedCount = 0;

        foreach ($paths as $path) {
            if ($this->deleteImage($path)) {
                $deletedCount++;
            }
        }

        return $deletedCount;
    }

    /**
     * Generate unique filename for image
     *
     * @param UploadedFile $file
     * @return string
     */
    protected function generateImageFilename(UploadedFile $file)
    {
        $extension = $file->getClientOriginalExtension();
        return time() . '_' . uniqid() . '.' . ($extension ?: 'jpg');
    }

    /**
     * Get image URL from storage path
     *
     * @param string|null $path
     * @param string|null $default
     * @return string
     */
    public function getImageUrl($path, $default = null)
    {
        if (!$path) {
            return $default ?: asset('images/default-car.jpg');
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return $default ?: asset('images/default-car.jpg');
    }

    /**
     * Create thumbnail from image
     *
     * @param string $originalPath
     * @param int $width
     * @param int $height
     * @param string $suffix
     * @return string|null
     */
    public function createThumbnail($originalPath, $width = 300, $height = 200, $suffix = '_thumb')
    {
        if (!Storage::disk('public')->exists($originalPath)) {
            return null;
        }

        $pathInfo = pathinfo($originalPath);
        $thumbnailPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . $suffix . '.jpg';

        $image = Image::make(Storage::disk('public')->path($originalPath))
            ->fit($width, $height)
            ->encode('jpg', 80);

        Storage::disk('public')->put($thumbnailPath, $image->stream());

        return $thumbnailPath;
    }

    /**
     * Validate image file
     *
     * @param UploadedFile $file
     * @param int $maxSizeKB
     * @param array $allowedTypes
     * @return array
     */
    public function validateImage(UploadedFile $file, $maxSizeKB = 2048, $allowedTypes = ['jpeg', 'jpg', 'png'])
    {
        $errors = [];

        // Check file size
        if ($file->getSize() > $maxSizeKB * 1024) {
            $errors[] = "Ukuran file tidak boleh lebih dari {$maxSizeKB}KB";
        }

        // Check file type
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $allowedTypes)) {
            $errors[] = "Format file harus: " . implode(', ', $allowedTypes);
        }

        // Check if file is actually an image
        if (!getimagesize($file->path())) {
            $errors[] = "File harus berupa gambar yang valid";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Get image dimensions
     *
     * @param string $path
     * @return array|null
     */
    public function getImageDimensions($path)
    {
        if (!Storage::disk('public')->exists($path)) {
            return null;
        }

        $imageInfo = getimagesize(Storage::disk('public')->path($path));
        
        if ($imageInfo) {
            return [
                'width' => $imageInfo[0],
                'height' => $imageInfo[1],
                'type' => $imageInfo['mime']
            ];
        }

        return null;
    }

    /**
     * Optimize existing image
     *
     * @param string $path
     * @param int $quality
     * @return bool
     */
    public function optimizeImage($path, $quality = 85)
    {
        if (!Storage::disk('public')->exists($path)) {
            return false;
        }

        try {
            $image = Image::make(Storage::disk('public')->path($path))
                ->encode('jpg', $quality);

            Storage::disk('public')->put($path, $image->stream());
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
