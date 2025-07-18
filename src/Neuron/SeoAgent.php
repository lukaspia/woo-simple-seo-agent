<?php

declare(strict_types=1);


namespace WooSimpleSeoAgent\Neuron;


use NeuronAI\Agent;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\Gemini\Gemini;

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
}