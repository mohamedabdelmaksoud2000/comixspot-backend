<?php

namespace App\Observers;

use App\Models\Post;
use App\Services\ImageConversionService;

class PostObserver
{
    protected $imageConversionService;

    public function __construct(ImageConversionService $imageConversionService)
    {
        $this->imageConversionService = $imageConversionService;
    }

    /**
     * Handle the Post "creating" event.
     */
    public function creating(Post $post): void
    {
        if ($post->image) {
            $webpPath = $this->imageConversionService->convertToWebp($post->image);
            if ($webpPath) {
                $post->image = $webpPath;
            }
        }
    }

    /**
     * Handle the Post "updating" event.
     */
    public function updating(Post $post): void
    {
        // Check if image was changed
        if ($post->isDirty('image') && $post->image) {
            $webpPath = $this->imageConversionService->convertToWebp($post->image);
            if ($webpPath) {
                $post->image = $webpPath;
            }
        }
    }
}
