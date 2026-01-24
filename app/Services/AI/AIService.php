<?php

namespace App\Services\AI;

use App\Models\AIProvider;
use App\Models\AIAction;
use App\Models\AIEmergencyStatus;
use App\Services\AI\Providers\GeminiProvider;
use App\Services\AI\Providers\OpenAIProvider;
use App\Services\AI\Providers\GroqProvider;
use Exception;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $providers = [];
    protected $emergencyKillSwitch = false;

    public function __construct()
    {
        $this->checkEmergencyStatus();
        $this->loadProviders();
    }

    protected function checkEmergencyStatus()
    {
        $status = AIEmergencyStatus::latest()->first();
        if ($status && $status->kill_switch_enabled) {
            $this->emergencyKillSwitch = true;
        }
    }

    protected function loadProviders()
    {
        $configs = AIProvider::where('is_active', true)
            ->orderBy('priority', 'desc') // Higher priority first
            ->get();

        foreach ($configs as $config) {
            try {
                // Decrypt credentials if stored roughly, or just use as array
                $key = $config->credentials['api_key'] ?? ($config->credentials['key'] ?? null);
                
                if (!$key) continue;

                if ($config->name === 'gemini') {
                    $this->providers[] = [
                        'instance' => new GeminiProvider($key, $config->model ?? 'gemini-pro'),
                        'model' => $config
                    ];
                } elseif ($config->name === 'openai') {
                    $this->providers[] = [
                        'instance' => new OpenAIProvider($key, $config->model ?? 'gpt-4'),
                        'model' => $config
                    ];
                } elseif ($config->name === 'groq') {
                    $this->providers[] = [
                        'instance' => new GroqProvider(['api_key' => $key, 'model' => $config->model ?? 'llama-3.3-70b-versatile']),
                        'model' => $config
                    ];
                }
            } catch (Exception $e) {
                Log::error("Failed to load AI provider {$config->name}: " . $e->getMessage());
            }
        }
        
        // Fallback: If no providers in DB, try env-based Groq
        if (empty($this->providers) && env('GROQ_API_KEY')) {
            $this->providers[] = [
                'instance' => new GroqProvider(['api_key' => env('GROQ_API_KEY')]),
                'model' => (object)['name' => 'groq', 'id' => null]
            ];
        }
    }

    /**
     * Main entry point for text generation with failover.
     */
    public function generateText(string $prompt, string $module, string $actionType, array $config = []): ?string
    {
        if ($this->emergencyKillSwitch) {
            $this->logAction($module, $actionType, ['prompt' => $prompt], null, 'failed', 'Kill Switch Active');
            throw new Exception("AI System is currently disabled by emergency kill switch.");
        }

        foreach ($this->providers as $providerData) {
            $provider = $providerData['instance'];
            $providerModel = $providerData['model'];

            try {
                $result = $provider->generateText($prompt, $config);
                
                // Log success
                $this->logAction(
                    $module, 
                    $actionType, 
                    ['prompt' => $prompt, 'config' => $config], 
                    $result, 
                    'executed', 
                    "Executed by {$providerModel->name}",
                    $providerModel->id,
                    $result['usage'] ?? []
                );

                return $result['content'];

            } catch (Exception $e) {
                Log::warning("AI Provider {$providerModel->name} failed: " . $e->getMessage());
                continue; // Try next provider
            }
        }

        $this->logAction($module, $actionType, ['prompt' => $prompt], null, 'failed', 'All providers failed');
        throw new Exception("All AI providers failed to generate response.");
    }

    /**
     * Analyze image with failover.
     */
    public function analyzeImage(string $imagePath, string $prompt, string $module, string $actionType): ?string
    {
        if ($this->emergencyKillSwitch) {
            throw new Exception("AI System is currently disabled.");
        }

        foreach ($this->providers as $providerData) {
            $provider = $providerData['instance'];
            $providerModel = $providerData['model'];

            try {
                $result = $provider->analyzeImage($imagePath, $prompt);
                
                $this->logAction(
                    $module,
                    $actionType,
                    ['image' => $imagePath, 'prompt' => $prompt],
                    $result,
                    'executed',
                    "Analyzed by {$providerModel->name}",
                    $providerModel->id,
                    $result['usage'] ?? []
                );

                return $result['content'];

            } catch (Exception $e) {
                Log::warning("AI Vision Provider {$providerModel->name} failed: " . $e->getMessage());
                continue;
            }
        }

        throw new Exception("All AI providers failed to analyze image.");
    }

    protected function logAction($module, $actionType, $input, $output, $status, $reasoning = null, $providerId = null, $usage = [])
    {
        // Calculate cost if usage data exists
        $cost = 0;
        if ($providerId && !empty($usage)) {
            // Find provider config again or assume it's passed?
            // Simplified cost calc
            // $cost = ...
        }

        AIAction::create([
            'module' => $module,
            'action_type' => $actionType,
            'description' => "AI Action: $actionType",
            'input_data' => $input,
            'output_data' => $output,
            'status' => $status,
            'reasoning' => $reasoning,
            'ai_provider_id' => $providerId,
            'was_auto_executed' => true,
            'executed_at' => now(),
            'tokens_used' => ($usage['input_tokens'] ?? 0) + ($usage['output_tokens'] ?? 0),
            'cost' => $cost
        ]);
    }
}
