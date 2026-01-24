<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Job: ProcessImageUpload
 * Handles image processing in queue (async)
 */

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProcessImageUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;

    public function __construct(
        public string $tempPath,
        public string $destinationPath,
        public string $purpose, // product, vendor_logo, banner
        public ?int $entityId = null,
        public array $sizes = []
    ) {
        $this->onQueue('images');
    }

    public function handle(): void
    {
        try {
            // Default sizes if not specified
            if (empty($this->sizes)) {
                $this->sizes = $this->getDefaultSizes();
            }

            $tempFile = Storage::disk('local')->path($this->tempPath);

            if (!file_exists($tempFile)) {
                throw new \Exception("Temp file not found: {$this->tempPath}");
            }

            // Process each size
            foreach ($this->sizes as $sizeName => $dimensions) {
                $outputPath = $this->generateOutputPath($sizeName);
                
                $image = Image::make($tempFile);
                
                // Resize maintaining aspect ratio
                $image->fit($dimensions['width'], $dimensions['height'], function ($constraint) {
                    $constraint->upsize();
                });
                
                // Optimize
                $image->encode('jpg', 85);
                
                // Save
                Storage::disk('public')->put($outputPath, $image->stream());
                
                Log::info("Image processed: {$outputPath}");
            }

            // Update temp file record
            DB::table('temp_files')
                ->where('file_path', $this->tempPath)
                ->update([
                    'processed' => true,
                    'moved_to_permanent' => true,
                    'updated_at' => now(),
                ]);

            // Clean up temp file
            Storage::disk('local')->delete($this->tempPath);

            Log::info("Image processing completed for: {$this->purpose}");

        } catch (\Exception $e) {
            Log::error("Image processing failed: " . $e->getMessage());
            throw $e;
        }
    }

    protected function getDefaultSizes(): array
    {
        return match ($this->purpose) {
            'product' => [
                'thumbnail' => ['width' => 150, 'height' => 150],
                'medium' => ['width' => 400, 'height' => 400],
                'large' => ['width' => 800, 'height' => 800],
            ],
            'vendor_logo' => [
                'small' => ['width' => 64, 'height' => 64],
                'medium' => ['width' => 128, 'height' => 128],
            ],
            'banner' => [
                'full' => ['width' => 1200, 'height' => 300],
            ],
            default => [
                'default' => ['width' => 400, 'height' => 400],
            ],
        };
    }

    protected function generateOutputPath(string $sizeName): string
    {
        $extension = pathinfo($this->destinationPath, PATHINFO_EXTENSION) ?: 'jpg';
        $baseName = pathinfo($this->destinationPath, PATHINFO_FILENAME);
        $directory = pathinfo($this->destinationPath, PATHINFO_DIRNAME);

        return "{$directory}/{$baseName}_{$sizeName}.{$extension}";
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Image processing job failed: " . $exception->getMessage());
        \App\Services\MetricsService::recordJobFailure(self::class, 'images');
    }
}
