<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Groq AI Provider Implementation
 */

namespace App\Services\AI\Providers;

use App\Services\AI\AIProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqProvider implements AIProviderInterface
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl = 'https://api.groq.com/openai/v1';

    public function __construct(array $config)
    {
        $this->apiKey = $config['api_key'] ?? env('GROQ_API_KEY', '');
        $this->model = $config['model'] ?? 'llama-3.3-70b-versatile';
    }

    public function generateText(string $prompt, array $options = []): string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => $options['model'] ?? $this->model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => $options['max_tokens'] ?? 1024,
                'temperature' => $options['temperature'] ?? 0.7,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? 'No response generated.';
            }

            Log::error('Groq API error', ['response' => $response->body()]);
            throw new \Exception('Groq API request failed: ' . $response->status());

        } catch (\Exception $e) {
            Log::error('Groq provider error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function generateChat(array $messages, array $options = []): string
    {
        try {
            $formattedMessages = array_map(function($msg) {
                return [
                    'role' => $msg['role'] ?? 'user',
                    'content' => $msg['content'] ?? ''
                ];
            }, $messages);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => $options['model'] ?? $this->model,
                'messages' => $formattedMessages,
                'max_tokens' => $options['max_tokens'] ?? 1024,
                'temperature' => $options['temperature'] ?? 0.7,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? 'No response generated.';
            }

            throw new \Exception('Groq chat request failed');

        } catch (\Exception $e) {
            Log::error('Groq chat error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function analyzeImage(string $imagePath, string $prompt, array $options = []): string
    {
        // Groq doesn't support vision yet, return a placeholder
        return 'Image analysis is not yet supported with Groq provider.';
    }

    public function calculateCost(int $inputTokens, int $outputTokens): float
    {
        // Groq is very affordable - approximate costs
        $inputCost = ($inputTokens / 1000) * 0.0001;  // ~$0.0001 per 1K tokens
        $outputCost = ($outputTokens / 1000) * 0.0002; // ~$0.0002 per 1K tokens
        return $inputCost + $outputCost;
    }
}
