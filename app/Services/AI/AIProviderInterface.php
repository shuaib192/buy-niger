<?php

namespace App\Services\AI;

interface AIProviderInterface
{
    /**
     * Generate text from a simple prompt.
     *
     * @return array [content, usage, meta]
     */
    public function generateText(string $prompt, array $config = []): array;

    /**
     * Generate a chat response from a history of messages.
     *
     * @param  array  $messages  format: [['role' => 'user', 'content' => '...']]
     * @return array [content, usage, meta]
     */
    public function generateChat(array $messages, array $config = []): array;

    /**
     * Analyze an image with a prompt.
     *
     * @return array [content, usage, meta]
     */
    public function analyzeImage(string $imagePath, string $prompt): array;

    /**
     * Calculate cost based on token usage.
     */
    public function calculateCost(int $inputTokens, int $outputTokens): float;
}
