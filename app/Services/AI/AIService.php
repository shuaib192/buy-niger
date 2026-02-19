<?php
/**
 * BuyNiger AI - Simple Groq AI Service
 * Written by Shuaibu Abdulmumin
 */

namespace App\Services\AI;

use App\Models\AIProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $apiKey;
    protected $model;
    protected $enabled = true;

    public function __construct()
    {
        $this->loadProvider();
    }

    protected function loadProvider()
    {
        // Try to get active Groq provider
        try {
            $provider = AIProvider::where('name', 'groq')
                ->where('is_active', true)
                ->first();

            if ($provider && isset($provider->credentials['api_key'])) {
                $this->apiKey = $provider->credentials['api_key'];
                $this->model = $provider->model ?? 'llama-3.3-70b-versatile';
                return;
            }

            // Fallback to env
            if (env('GROQ_API_KEY')) {
                $this->apiKey = env('GROQ_API_KEY');
                $this->model = 'llama-3.3-70b-versatile';
                return;
            }

            $this->enabled = false;
        } catch (\Exception $e) {
            Log::error('AI Service init error: ' . $e->getMessage());
            $this->enabled = false;
        }
    }

    public function isEnabled(): bool
    {
        return $this->enabled && !empty($this->apiKey);
    }

    /**
     * Generate text using Groq API
     */
    public function generateText(string $prompt, string $module = '', string $action = '', array $options = []): string
    {
        if (!$this->isEnabled()) {
            throw new \Exception('AI is not configured. Please add your Groq API key in settings.');
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'max_tokens' => $options['max_tokens'] ?? 500,
                    'temperature' => $options['temperature'] ?? 0.7,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? 'No response generated.';
            }

            Log::error('Groq API Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            throw new \Exception('AI request failed: ' . $response->status());

        } catch (\Exception $e) {
            Log::error('AI generateText error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Chat with history
     */
    public function chat(array $messages, array $options = []): string
    {
        if (!$this->isEnabled()) {
            throw new \Exception('AI is not configured.');
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $this->model,
                    'messages' => $messages,
                    'max_tokens' => $options['max_tokens'] ?? 500,
                    'temperature' => $options['temperature'] ?? 0.7,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? 'No response.';
            }

            throw new \Exception('Chat failed: ' . $response->status());

        } catch (\Exception $e) {
            Log::error('AI chat error: ' . $e->getMessage());
            throw $e;
        }
    }
}
