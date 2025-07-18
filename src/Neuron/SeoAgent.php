<?php

declare(strict_types=1);


namespace WooSimpleSeoAgent\Neuron;


use NeuronAI\Agent;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\Gemini\Gemini;
use NeuronAI\SystemPrompt;

/**
 * Class SeoAgent
 *
 * @package WooSimpleSeoAgent\Neuron
 */
class SeoAgent extends Agent
{
    /**
     * @return \NeuronAI\Providers\AIProviderInterface
     */
    public function provider(): AIProviderInterface
    {
        $configPath = dirname(__DIR__, 2) . '/config.php';
        $config = [];

        if (file_exists($configPath)) {
            $config = require $configPath;
        }

        $apiKey = $config['gemini']['api_key'] ?? '';
        $model = $config['gemini']['model'] ?? 'gemini-2.0-flash';

        if (empty($apiKey) || $apiKey === 'YOUR_GEMINI_API_KEY_HERE') {
            throw new \RuntimeException('Missing Gemini API key. Please set it in the config file. See: ' . $configPath);
        }

        return new Gemini(
            key: $apiKey,
            model: $model
        );
    }

    /**
     * @return string
     */
    public function instructions(): string
    {
        return (string) new SystemPrompt(
            background: ["You are an AI Agent specialized in SEO."],
            steps: [
                "Analise provided data.",
                "Depends on data, evaluate if descriptions are good for SEO, and possibly propose new version of description.",
                "Ignore tasks that are not related to SEO.",
                "Write the summary.",
                        ],
            output: [
                "Write a evaluation summary as list. Use just fluent text.",
                "Write possible improvements."
                            ]
        );
    }
}