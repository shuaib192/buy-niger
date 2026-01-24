<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Job: SendPushNotification
 * Handles notification dispatch in queue (async)
 */

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SendPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;

    public function __construct(
        public int $userId,
        public string $type,
        public string $title,
        public string $message,
        public ?string $actionUrl = null,
        public array $data = []
    ) {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        try {
            // Create in-app notification
            DB::table('notifications')->insert([
                'id' => Str::uuid(),
                'user_id' => $this->userId,
                'type' => $this->type,
                'title' => $this->title,
                'message' => $this->message,
                'action_url' => $this->actionUrl,
                'data' => json_encode($this->data),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info("Notification sent to user {$this->userId}: {$this->title}");

        } catch (\Exception $e) {
            Log::error("Notification failed for user {$this->userId}: " . $e->getMessage());
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Notification job failed: " . $exception->getMessage());
        \App\Services\MetricsService::recordJobFailure(self::class, 'notifications');
    }
}
