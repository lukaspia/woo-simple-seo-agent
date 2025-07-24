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
            "Improve seo elements if needed.",
            'Change only elements that are indicate as "Additional request". If element is not mentioned return empty ("").',
            "In case you do something with keywords/tags, return maximum of 4 keywords.",
            //"Take into account requirements of GEO (Generative Engine Optimization) in you tasks.",
            //"Use the tools you have available to retrieve the product data you need. If no tools are available, ask user for data.",
            //"Analise provided data.",
            //"Depends on data, evaluate if title, descriptions, keywords and short description are good for SEO, and possibly propose new version of it.",
            //"If some data are not provided, and you have no tools to retrieve it, ignore it or propose output for this data.",
            //"In description you can use html tags if needed (for example to highlight important words).",
            //"Ignore tasks that are not related to SEO and process of retrieves data that you require to do SEO tasks.",
            "Write the summary of the evaluation, where you mention the possible improvements or other messages for user.",
        ];

        $output = [
            //"Write a evaluation summary with possible improvements as a list.",
            "Return everything as json with fields: title, description, keywords, shortDescription and summary. That part of output write in pl_PL."
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