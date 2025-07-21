<?php

declare(strict_types=1);


namespace WooSimpleSeoAgent\Neuron;


use NeuronAI\Agent;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\Gemini\Gemini;
use NeuronAI\SystemPrompt;
use NeuronAI\Tools\PropertyType;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;

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
            throw new \RuntimeException(
                'Missing Gemini API key. Please set it in the config file. See: ' . $configPath
            );
        }

        return new Gemini(
            key:   $apiKey,
            model: $model
        );
    }

    /**
     * @return string
     */
    public function instructions(): string
    {
        return (string)new SystemPrompt(
            background: ["You are an AI Agent specialized in SEO."],
            steps:      [
                            "Use the tools you have available to retrieve the product data you need.",
                            "Analise provided data.",
                            "Depends on data, evaluate if descriptions are good for SEO, and possibly propose new version of description.",
                            "Ignore tasks that are not related to SEO.",
                            "Write the summary.",
                        ],
            output:     [
                            "Write a evaluation summary as list. Use just fluent text.",
                            "Write possible improvements."
                        ]
        );
    }

    protected function tools(): array
    {
        return [
            Tool::make(
                'get_product_data',
                'Get product data from database.',
            )->addProperty(
                new ToolProperty(
                    name:        'productId',
                    type:        PropertyType::INTEGER,
                    description: 'Id of the product.',
                    required:    true
                )
            )->setCallable(function (string $productId) {
                return [
                    'description' => wc_get_product($productId)->get_description()
                ];
            })
        ];
    }
}