<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Job: CleanupTempFiles
 * Scheduled job to clean expired temp files
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

class CleanupTempFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        $this->onQueue('maintenance');
    }

    public function handle(): void
    {
        try {
            // Get expired temp files
            $expiredFiles = DB::table('temp_files')
                ->where('expires_at', '<', now())
                ->where('moved_to_permanent', false)
                ->get();

            $deletedCount = 0;

            foreach ($expiredFiles as $file) {
                try {
                    // Delete from storage
                    if (Storage::disk('local')->exists($file->file_path)) {
                        Storage::disk('local')->delete($file->file_path);
                    }

                    // Remove record
                    DB::table('temp_files')->where('id', $file->id)->delete();
                    $deletedCount++;

                } catch (\Exception $e) {
                    Log::warning("Failed to delete temp file {$file->file_path}: " . $e->getMessage());
                }
            }

            Log::info("Temp file cleanup completed. Deleted: {$deletedCount} files");

        } catch (\Exception $e) {
            Log::error("Temp file cleanup failed: " . $e->getMessage());
            throw $e;
        }
    }
}
