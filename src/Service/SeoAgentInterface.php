<?php

declare(strict_types=1);

namespace WooSimpleSeoAgent\Service;

interface SeoAgentInterface
{
    /**
     * Generate SEO content based on the given prompt and context
     * 
     * @param string $prompt The main prompt for content generation
     * @param array $context Additional context including conversation history
     * @return array Decoded JSON response from the AI
     * @throws \JsonException If the response cannot be decoded
     */
    public function generateSeoContent(string $prompt, array $context = []): array;
}
