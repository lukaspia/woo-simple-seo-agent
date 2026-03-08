<?php

declare(strict_types=1);

namespace WooSimpleSeoAgent\Service;

use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\StructuredOutput\JsonExtractor;
use WooSimpleSeoAgent\Neuron\SeoAgent as NeuronSeoAgent;
use WooSimpleSeoAgent\Service\SeoAgentInterface;

final readonly class NeuronSeoAgentAdapter implements SeoAgentInterface
{
    public function __construct(
        private NeuronSeoAgent $seoAgent,
        private JsonExtractor $jsonExtractor
    ) {
    }

    /**
     * @param string $prompt
     * @param array $context
     * @return array
     * @throws \JsonException
     * @throws \Throwable
     */
    public function generateSeoContent(string $prompt, array $context = []): array
    {
        $fullPrompt = $this->enrichPromptWithContext($prompt, $context);

        $response = $this->seoAgent->chat(
            new UserMessage($fullPrompt)
        );

        $rawContent = $response->getContent();
        $seoJson = $this->jsonExtractor->getJson($rawContent);

        if (empty($seoJson)) {
            throw new \RuntimeException(
                "AI failed to return valid JSON tags. Raw response: " . substr($rawContent, 0, 100)
            );
        }

        return json_decode($seoJson, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param string $prompt
     * @param array $context
     * @return string
     */
    private function enrichPromptWithContext(string $prompt, array $context): string
    {
        if (empty($context['conversationHistory'])) {
            return $prompt;
        }

        $history = is_array($context['conversationHistory'])
            ? implode("\n- ", $context['conversationHistory'])
            : $context['conversationHistory'];

        return sprintf(
            "%s\n\n### Previous Conversation Context:\n- %s",
            $prompt,
            $history
        );
    }
}
