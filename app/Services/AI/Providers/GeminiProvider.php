<?php

namespace App\Services\AI\Providers;

use App\Services\AI\AIProviderInterface;
use Illuminate\Support\Facades\Http;
use Exception;

class GeminiProvider implements AIProviderInterface
{
    protected $apiKey;
    protected $model;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct(string $apiKey, string $model = 'gemini-pro')
    {
        $this->apiKey = $apiKey;
        $this->model = $model;
    }

    public function generateText(string $prompt, array $config = []): array
    {
        // Gemini Text Generation (using generateContent)
        $url = "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}";

        $response = Http::post($url, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => $config['temperature'] ?? 0.7,
                'maxOutputTokens' => $config['max_tokens'] ?? 1000,
            ]
        ]);

        if ($response->failed()) {
            throw new Exception("Gemini API Error: " . $response->body());
        }

        $data = $response->json();
        
        $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
        
        // Gemini doesn't always return token usage in simple response without extra config, 
        // but let's check metadata or estimate/default to 0 if missing.
        // Recent API updates do include usageMetadata
        $usage = $data['usageMetadata'] ?? ['promptTokenCount' => 0, 'candidatesTokenCount' => 0];

        return [
            'content' => $content,
            'usage' => [
                'input_tokens' => $usage['promptTokenCount'] ?? 0,
                'output_tokens' => $usage['candidatesTokenCount'] ?? 0,
            ],
            'meta' => $data
        ];
    }

    public function generateChat(array $messages, array $config = []): array
    {
        // Convert standard messages format to Gemini format
        // Standard: [['role' => 'user', 'content' => '...']]
        // Gemini: [['role' => 'user'|'model', 'parts' => [['text' => '...']]]]
        
        $geminiMessages = array_map(function ($msg) {
            return [
                'role' => $msg['role'] === 'assistant' ? 'model' : 'user', // Gemini uses 'model' for assistant
                'parts' => [['text' => $msg['content']]]
            ];
        }, $messages);

        $url = "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}";
        
        $response = Http::post($url, [
            'contents' => $geminiMessages,
            'generationConfig' => [
                'temperature' => $config['temperature'] ?? 0.7,
                'maxOutputTokens' => $config['max_tokens'] ?? 1000,
            ]
        ]);

        if ($response->failed()) {
            throw new Exception("Gemini Chat API Error: " . $response->body());
        }

        $data = $response->json();
        $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $usage = $data['usageMetadata'] ?? ['promptTokenCount' => 0, 'candidatesTokenCount' => 0];

        return [
            'content' => $content,
            'usage' => [
                'input_tokens' => $usage['promptTokenCount'] ?? 0,
                'output_tokens' => $usage['candidatesTokenCount'] ?? 0,
            ],
            'meta' => $data
        ];
    }

    public function analyzeImage(string $imagePath, string $prompt): array
    {
        // Determine model for vision (e.g., gemini-pro-vision)
        $visionModel = 'gemini-1.5-flash'; // Or gemini-pro-vision depending on availability
        
        $url = "{$this->baseUrl}/{$visionModel}:generateContent?key={$this->apiKey}";

        // Read image and base64 encode
        if (!file_exists($imagePath)) {
            throw new Exception("Image not found: $imagePath");
        }
        
        $imageData = base64_encode(file_get_contents($imagePath));
        $mimeType = mime_content_type($imagePath);

        $response = Http::post($url, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $imageData
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        if ($response->failed()) {
            throw new Exception("Gemini Vision API Error: " . $response->body());
        }

        $data = $response->json();
        $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $usage = $data['usageMetadata'] ?? ['promptTokenCount' => 0, 'candidatesTokenCount' => 0];

        return [
            'content' => $content,
            'usage' => [
                'input_tokens' => $usage['promptTokenCount'] ?? 0,
                'output_tokens' => $usage['candidatesTokenCount'] ?? 0,
            ],
            'meta' => $data
        ];
    }

    public function calculateCost(int $inputTokens, int $outputTokens): float
    {
        // Pricing depends on model. Hardcoding generic estimates or fetching from DB would be better.
        // For Gemini Pro (free tier often, but let's assume paid tier pricing)
        // Input: $0.125 / 1M chars ~ $0.50 / 1M tokens? 
        // Output: $0.375 / 1M chars
        // Let's use generic placeholders: $0.0005 per 1k input, $0.0015 per 1k output
        
        $inputCost = ($inputTokens / 1000) * 0.0005;
        $outputCost = ($outputTokens / 1000) * 0.0015;
        
        return $inputCost + $outputCost;
    }
}
