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
use WooSimpleSeoAgent\Dto\Seo;

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
        $model = $config['gemini']['model'] ?? 'gemini-2.5-flash';

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
        $steps = [
            'Improve seo elements if needed. If not, leave them empty (as their value use "").',
            'Change only elements that are indicate as "Additional request". If element is not mentioned in "Additional request" return them empty (as their value use "").',
            "Take into account requirements of GEO (Generative Engine Optimization) in you tasks.",
            "In case you do something with keywords/tags, return maximum of 4 keywords.",
            "In case you do something with description, you can use html tags if needed (for example to highlight important words or list things).",
            "Write the summary of the evaluation, where you made the possible improvements or add other messages for user. Except simple text, you can use html tags to list things. You can propose further tasks and improvements if needed.",
        ];

        $output = [
            "Return everything as json with fields: title, description, keywords, shortDescription and summary. That part of output write in " . get_locale() . '.',
            'If you dont change anything in some specific filed, leave it empty (as their value use ""). Remember that filed not mentioned in "Additional request" should be empty (as their value use "").',
        ];

        return (string)new SystemPrompt(
            background: ["You are an AI Agent specialized in SEO."],
            steps:      apply_filters('wssa_agent_steps', $steps),
            output:     apply_filters('wssa_agent_output', $output)
        );
    }

    /**
     * @return array|\NeuronAI\Tools\ToolInterface[]|\NeuronAI\Tools\Toolkits\ToolkitInterface[]
     */
    protected function tools(): array
    {
        return [
            Tool::make(
                'get_product_data',
                'Use this tool to retrieve product data if there are no provided.',
            )->addProperty(
                new ToolProperty(
                    name:        'productId',
                    type:        PropertyType::INTEGER,
                    description: 'Id of the product.',
                    required:    true
                )
            )->setCallable(function (int $productId) {
                $product = wc_get_product($productId);

                return [
                    'title' => $product->get_title(),
                    'description' => $product->get_description(),
                    'shortDescription' => $product->get_short_description(),
                    'keywords' => $this->getKeywords($productId),
                ];
            })
        ];
    }

    /**
     * @return string
     */
    protected function getOutputClass(): string
    {
        return Seo::class;
    }

    private function getKeywords(int $productId): string
    {
        $productTags = get_the_terms($productId, 'product_tag');

        if (!empty($productTags) && !is_wp_error($productTags)) {
            $tagNames = [];
            foreach ($productTags as $tag) {
                $tagNames[] = $tag->name;
            }

            return implode(', ', $tagNames);
        }

        return '';
    }
}