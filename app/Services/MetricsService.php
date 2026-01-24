<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Service: MetricsService
 * Handles system health metrics and observability
 */

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MetricsService
{
    /**
     * Record a job failure metric
     */
    public static function recordJobFailure(string $jobClass, string $queue): void
    {
        try {
            $today = now()->toDateString();

            DB::table('job_metrics')->updateOrInsert(
                [
                    'job_class' => $jobClass,
                    'queue' => $queue,
                    'metric_date' => $today,
                ],
                [
                    'failed_count' => DB::raw('failed_count + 1'),
                    'last_failed_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Log system health metric
            DB::table('system_health_metrics')->insert([
                'metric_type' => 'job_failure',
                'metric_name' => $jobClass,
                'value' => 1,
                'unit' => 'count',
                'status' => 'warning',
                'metadata' => json_encode(['queue' => $queue]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to record job failure metric: " . $e->getMessage());
        }
    }

    /**
     * Record job success
     */
    public static function recordJobSuccess(string $jobClass, string $queue, float $processingTimeMs): void
    {
        try {
            $today = now()->toDateString();

            // Update daily metrics
            DB::table('job_metrics')->updateOrInsert(
                [
                    'job_class' => $jobClass,
                    'queue' => $queue,
                    'metric_date' => $today,
                ],
                [
                    'processed_count' => DB::raw('processed_count + 1'),
                    'avg_processing_time' => DB::raw("(avg_processing_time * processed_count + {$processingTimeMs}) / (processed_count + 1)"),
                    'last_processed_at' => now(),
                    'updated_at' => now(),
                ]
            );

        } catch (\Exception $e) {
            Log::error("Failed to record job success metric: " . $e->getMessage());
        }
    }

    /**
     * Record queue health
     */
    public static function recordQueueHealth(string $queue, int $pendingJobs, int $failedJobs): void
    {
        try {
            $status = 'normal';
            if ($failedJobs > 10) $status = 'warning';
            if ($failedJobs > 50 || $pendingJobs > 1000) $status = 'critical';

            DB::table('system_health_metrics')->insert([
                'metric_type' => 'queue_health',
                'metric_name' => $queue,
                'value' => $pendingJobs,
                'unit' => 'pending_jobs',
                'status' => $status,
                'metadata' => json_encode([
                    'pending' => $pendingJobs,
                    'failed' => $failedJobs,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to record queue health: " . $e->getMessage());
        }
    }

    /**
     * Record AI latency
     */
    public static function recordAILatency(string $provider, float $latencyMs): void
    {
        try {
            $status = 'normal';
            if ($latencyMs > 5000) $status = 'warning';
            if ($latencyMs > 15000) $status = 'critical';

            DB::table('system_health_metrics')->insert([
                'metric_type' => 'ai_latency',
                'metric_name' => $provider,
                'value' => $latencyMs,
                'unit' => 'ms',
                'status' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to record AI latency: " . $e->getMessage());
        }
    }

    /**
     * Record payment failure
     */
    public static function recordPaymentFailure(string $gateway, string $reason): void
    {
        try {
            DB::table('system_health_metrics')->insert([
                'metric_type' => 'payment_failure',
                'metric_name' => $gateway,
                'value' => 1,
                'unit' => 'count',
                'status' => 'warning',
                'metadata' => json_encode(['reason' => $reason]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to record payment failure: " . $e->getMessage());
        }
    }

    /**
     * Get system health summary
     */
    public static function getHealthSummary(): array
    {
        $lastHour = now()->subHour();

        return [
            'queue_status' => self::getQueueStatus(),
            'recent_failures' => DB::table('system_health_metrics')
                ->where('metric_type', 'job_failure')
                ->where('created_at', '>=', $lastHour)
                ->count(),
            'ai_avg_latency' => DB::table('system_health_metrics')
                ->where('metric_type', 'ai_latency')
                ->where('created_at', '>=', $lastHour)
                ->avg('value') ?? 0,
            'payment_failures' => DB::table('system_health_metrics')
                ->where('metric_type', 'payment_failure')
                ->where('created_at', '>=', $lastHour)
                ->count(),
            'overall_status' => self::calculateOverallStatus(),
        ];
    }

    /**
     * Get queue status
     */
    private static function getQueueStatus(): array
    {
        $queues = ['payments', 'emails', 'notifications', 'ai', 'search', 'analytics', 'images', 'maintenance'];
        $status = [];

        foreach ($queues as $queue) {
            $pending = DB::table('jobs')->where('queue', $queue)->count();
            $failed = DB::table('failed_jobs')->where('queue', $queue)->count();
            
            $status[$queue] = [
                'pending' => $pending,
                'failed' => $failed,
                'status' => $failed > 10 ? 'warning' : ($pending > 100 ? 'busy' : 'healthy'),
            ];
        }

        return $status;
    }

    /**
     * Calculate overall system status
     */
    private static function calculateOverallStatus(): string
    {
        $lastHour = now()->subHour();

        $criticalMetrics = DB::table('system_health_metrics')
            ->where('status', 'critical')
            ->where('created_at', '>=', $lastHour)
            ->count();

        $warningMetrics = DB::table('system_health_metrics')
            ->where('status', 'warning')
            ->where('created_at', '>=', $lastHour)
            ->count();

        if ($criticalMetrics > 0) return 'critical';
        if ($warningMetrics > 5) return 'warning';
        return 'healthy';
    }
}
