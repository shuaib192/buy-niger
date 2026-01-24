<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Job: SendEmailNotification
 * Handles email sending in queue (async)
 */

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class SendEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [10, 30, 60];

    public function __construct(
        public string $recipientEmail,
        public string $recipientName,
        public string $templateName,
        public array $data = [],
        public ?int $userId = null,
        public ?int $campaignId = null
    ) {
        $this->onQueue('emails');
    }

    public function handle(): void
    {
        try {
            // Get template
            $template = DB::table('email_templates')
                ->where('name', $this->templateName)
                ->where('is_active', true)
                ->first();

            if (!$template) {
                Log::warning("Email template not found: {$this->templateName}");
                return;
            }

            // Parse template variables
            $subject = $this->parseTemplate($template->subject, $this->data);
            $body = $this->parseTemplate($template->body, $this->data);

            // Send email
            Mail::send([], [], function ($message) use ($subject, $body) {
                $message->to($this->recipientEmail, $this->recipientName)
                    ->subject($subject)
                    ->html($body);
            });

            // Log successful send
            DB::table('email_logs')->insert([
                'user_id' => $this->userId,
                'email' => $this->recipientEmail,
                'subject' => $subject,
                'template' => $this->templateName,
                'campaign_id' => $this->campaignId,
                'status' => 'sent',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info("Email sent to {$this->recipientEmail} using template {$this->templateName}");

        } catch (\Exception $e) {
            // Log failure
            DB::table('email_logs')->insert([
                'user_id' => $this->userId,
                'email' => $this->recipientEmail,
                'subject' => 'Failed to send',
                'template' => $this->templateName,
                'campaign_id' => $this->campaignId,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::error("Email failed to {$this->recipientEmail}: " . $e->getMessage());
            throw $e;
        }
    }

    protected function parseTemplate(string $template, array $data): string
    {
        foreach ($data as $key => $value) {
            $template = str_replace("{{$key}}", $value, $template);
        }
        return $template;
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Email job failed for {$this->recipientEmail}: " . $exception->getMessage());
        \App\Services\MetricsService::recordJobFailure(self::class, 'emails');
    }
}
