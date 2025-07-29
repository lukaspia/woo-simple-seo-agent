<?php

declare(strict_types=1);

namespace WooSimpleSeoAgent\Service;

use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\StructuredOutput\JsonExtractor;
use WooSimpleSeoAgent\Neuron\SeoAgent as NeuronSeoAgent;

readonly class NeuronSeoAgentAdapter implements SeoAgentInterface
{
    public function __construct(
        private NeuronSeoAgent $seoAgent,
        private JsonExtractor $jsonExtractor
    ) {
    }

    public function generateSeoContent(string $prompt, array $context = []): array
    {
        if (!empty($context['conversationHistory'])) {
            $prompt .= ". Here is our conversation history: " .
                implode(",", $context['conversationHistory']);
        }

        $response = $this->seoAgent->chat(
            new UserMessage($prompt)
        );

        $seoJson = $this->jsonExtractor->getJson($response->getContent());
        return json_decode($seoJson, true, 512, JSON_THROW_ON_ERROR);
    }
}
