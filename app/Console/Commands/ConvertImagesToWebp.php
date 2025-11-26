<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\ImageConversionService;
use Illuminate\Console\Command;

class ConvertImagesToWebp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:convert-to-webp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert all existing post images to WebP format';

    protected $imageConversionService;

    /**
     * Create a new command instance.
     */
    public function __construct(ImageConversionService $imageConversionService)
    {
        parent::__construct();
        $this->imageConversionService = $imageConversionService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting image conversion to WebP...');

        // Get all posts with non-WebP images
        $posts = Post::whereNotNull('image')
            ->where('image', 'not like', '%.webp')
            ->get();

        if ($posts->isEmpty()) {
            $this->info('No images to convert. All images are already in WebP format.');
            return 0;
        }

        $this->info("Found {$posts->count()} images to convert.");

        $converted = 0;
        $failed = 0;

        $progressBar = $this->output->createProgressBar($posts->count());
        $progressBar->start();

        foreach ($posts as $post) {
            $webpPath = $this->imageConversionService->convertToWebp($post->image);

            if ($webpPath) {
                $post->image = $webpPath;
                $post->save();
                $converted++;
            } else {
                $failed++;
                $this->newLine();
                $this->error("Failed to convert image for post ID: {$post->id}");
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("Conversion complete!");
        $this->info("Successfully converted: {$converted} images");

        if ($failed > 0) {
            $this->warn("Failed to convert: {$failed} images");
        }

        return 0;
    }
}
