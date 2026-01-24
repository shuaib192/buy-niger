<?php

namespace App\Services\AI\Providers;

use App\Services\AI\AIProviderInterface;
use Illuminate\Support\Facades\Http;
use Exception;

class OpenAIProvider implements AIProviderInterface
{
    protected $apiKey;
    protected $model;
    protected $baseUrl = 'https://api.openai.com/v1';

    public function __construct(string $apiKey, string $model = 'gpt-4')
    {
        $this->apiKey = $apiKey;
        $this->model = $model;
    }

    public function generateText(string $prompt, array $config = []): array
    {
        // OpenAI Chat Completion (legacy completion is deprecated)
        return $this->generateChat([['role' => 'user', 'content' => $prompt]], $config);
    }

    public function generateChat(array $messages, array $config = []): array
    {
        $url = "{$this->baseUrl}/chat/completions";
        
        $response = Http::withToken($this->apiKey)->post($url, [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => $config['temperature'] ?? 0.7,
            'max_tokens' => $config['max_tokens'] ?? 1000,
        ]);

        if ($response->failed()) {
            throw new Exception("OpenAI API Error: " . $response->body());
        }

        $data = $response->json();
        $content = $data['choices'][0]['message']['content'] ?? '';
        $usage = $data['usage'] ?? ['prompt_tokens' => 0, 'completion_tokens' => 0];

        return [
            'content' => $content,
            'usage' => [
                'input_tokens' => $usage['prompt_tokens'],
                'output_tokens' => $usage['completion_tokens'],
            ],
            'meta' => $data
        ];
    }

    public function analyzeImage(string $imagePath, string $prompt): array
    {
        $url = "{$this->baseUrl}/chat/completions";
        
        // Open/Read image
        if (!file_exists($imagePath)) {
            throw new Exception("Image not found: $imagePath");
        }
        
        $imageData = base64_encode(file_get_contents($imagePath));
        $mimeType = mime_content_type($imagePath);

        $response = Http::withToken($this->apiKey)->post($url, [
            'model' => 'gpt-4-vision-preview', // Or gpt-4o
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => $prompt],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:{$mimeType};base64,{$imageData}"
                            ]
                        ]
                    ]
                ]
            ],
            'max_tokens' => 1000
        ]);

        if ($response->failed()) {
            throw new Exception("OpenAI Vision API Error: " . $response->body());
        }

        $data = $response->json();
        $content = $data['choices'][0]['message']['content'] ?? '';
        $usage = $data['usage'] ?? ['prompt_tokens' => 0, 'completion_tokens' => 0];

        return [
            'content' => $content,
            'usage' => [
                'input_tokens' => $usage['prompt_tokens'],
                'output_tokens' => $usage['completion_tokens'],
            ],
            'meta' => $data
        ];
    }

    public function calculateCost(int $inputTokens, int $outputTokens): float
    {
        // GPT-4 pricing approx
        // Input: $0.03 / 1k
        // Output: $0.06 / 1k
        
        $inputCost = ($inputTokens / 1000) * 0.03;
        $outputCost = ($outputTokens / 1000) * 0.06;
        
        return $inputCost + $outputCost;
    }
}
