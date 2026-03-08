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
use WooSimpleSeoAgent\Dto\SeoDto;
use WooSimpleSeoAgent\Repository\ProductRepositoryInterface;

final class SeoAgent extends Agent
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $model,
        private readonly string $locale,
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }

    /**
     * @return \NeuronAI\Providers\AIProviderInterface
     */
    public function provider(): AIProviderInterface
    {
        return new Gemini(
            key:   $this->apiKey,
            model: $this->model
        );
    }

    /**
     * @return string
     */
    public function instructions(): string
    {
        $steps = [
            'Improve seo elements if needed. If not, leave them empty (as their value use "").',
            'Change only elements that are indicated as "Additional request". If element is not mentioned in "Additional request" return them empty (as their value use "").',
            "Take into account requirements of GEO (Generative Engine Optimization) in your tasks.",
            "In case you do something with keywords/tags, return maximum of 4 keywords.",
            "In case you do something with description, you can use html tags if needed.",
            "Write the summary of the evaluation, where you made the possible improvements. You can use html tags.",
        ];

        $output = [
            "Return everything as json with fields: title, description, keywords, shortDescription and summary. That part of output write in " . $this->locale . '.',
            'If you dont change anything in some specific field, leave it empty (as their value use "").',
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
                return $this->productRepository->getProductDataForSeo($productId);
            })
        ];
    }

    /**
     * @return string
     */
    protected function getOutputClass(): string
    {
        return SeoDto::class;
    }
}