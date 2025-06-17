<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Upload and optimize image
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder
     * @param int $width
     * @param int $height
     * @return string
     */
    protected function uploadImage($file, $folder = 'images', $width = 800, $height = 600)
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $folder . '/' . $filename;

        // Resize and optimize image
        $image = Image::make($file)
            ->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode('jpg', 85);

        Storage::disk('public')->put($path, $image->stream());

        return $path;
    }

    /**
     * Delete image from storage
     *
     * @param string $path
     * @return bool
     */
    protected function deleteImage($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }

    /**
     * Format price to Indonesian Rupiah
     *
     * @param float $amount
     * @return string
     */
    protected function formatRupiah($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Calculate days between two dates
     *
     * @param string $startDate
     * @param string $endDate
     * @return int
     */
    protected function calculateDays($startDate, $endDate)
    {
        return \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;
    }
}
