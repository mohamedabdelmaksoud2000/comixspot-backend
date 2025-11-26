<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class ImageConversionService
{
    /**
     * Convert an image to WebP format
     * 
     * @param string $imagePath The path to the image relative to storage disk
     * @param string $disk The storage disk (default: 'public')
     * @param int $quality The quality of the WebP image (default: 85)
     * @return string|null The new WebP image path or null on failure
     */
    public function convertToWebp(string $imagePath, string $disk = 'public', int $quality = 85): ?string
    {
        try {
            // Get the full path to the image
            $fullPath = Storage::disk($disk)->path($imagePath);
            
            // Check if file exists
            if (!Storage::disk($disk)->exists($imagePath)) {
                return null;
            }

            // Check if already a WebP image
            if (pathinfo($imagePath, PATHINFO_EXTENSION) === 'webp') {
                return $imagePath;
            }

            // Load the image
            $image = Image::read($fullPath);

            // Generate new WebP filename
            $pathInfo = pathinfo($imagePath);
            $webpPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
            $webpFullPath = Storage::disk($disk)->path($webpPath);

            // Convert and save as WebP
            $image->toWebp($quality)->save($webpFullPath);

            // Delete the original image
            Storage::disk($disk)->delete($imagePath);

            return $webpPath;
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Image conversion to WebP failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Convert multiple images to WebP format
     * 
     * @param array $imagePaths Array of image paths
     * @param string $disk The storage disk (default: 'public')
     * @param int $quality The quality of the WebP image (default: 85)
     * @return array Array of converted image paths
     */
    public function convertMultipleToWebp(array $imagePaths, string $disk = 'public', int $quality = 85): array
    {
        $convertedPaths = [];

        foreach ($imagePaths as $imagePath) {
            $webpPath = $this->convertToWebp($imagePath, $disk, $quality);
            if ($webpPath) {
                $convertedPaths[] = $webpPath;
            }
        }

        return $convertedPaths;
    }
}
