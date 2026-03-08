<?php

declare(strict_types=1);

namespace WooSimpleSeoAgent;

use NeuronAI\StructuredOutput\JsonExtractor;
use WooSimpleSeoAgent\Assets\AssetManager;
use WooSimpleSeoAgent\Controller\Admin\ProductSeoMetaboxController;
use WooSimpleSeoAgent\Controller\Rest\AgentSeoController;
use WooSimpleSeoAgent\Controller\Rest\ProductMetaController;
use WooSimpleSeoAgent\Repository\ProductRepository;
use WooSimpleSeoAgent\Rest\RestRouteRegistrar;
use WooSimpleSeoAgent\Service\NeuronSeoAgentAdapter;
use WooSimpleSeoAgent\Service\PromptBuilder;
use WooSimpleSeoAgent\View\ViewRenderer;
use WooSimpleSeoAgent\Neuron\SeoAgent as NeuronSeoAgent;

final class ContainerBuilder
{
    /**
     * @param string $mainFile
     * @return \WooSimpleSeoAgent\ServiceContainer
     */
    public static function build(string $mainFile): ServiceContainer
    {
        $basePath = plugin_dir_path($mainFile);
        $baseUrl = plugin_dir_url($mainFile);

        $renderer = new ViewRenderer();
        $assets = new AssetManager($basePath, $baseUrl);
        $jsonExtractor = new JsonExtractor();
        $neuronSeo = new NeuronSeoAgent();
        $promptBuilder = new PromptBuilder();

        $productRepository = new ProductRepository();

        $seoAdapter = new NeuronSeoAgentAdapter($neuronSeo, $jsonExtractor);

        $restControllers = [
            new AgentSeoController($seoAdapter, $promptBuilder),
            new ProductMetaController($productRepository),
        ];

        $api = new RestRouteRegistrar($restControllers);

        $metabox = new ProductSeoMetaboxController($basePath . 'templates/', $renderer);

        return new ServiceContainer(
            services:    [
                             'renderer' => $renderer,
                             'assets' => $assets,
                             'seo_adapter' => $seoAdapter,
                             'product_repository' => $productRepository,
                         ],
            controllers: [
                             'metabox' => $metabox,
                             'api' => $api,
                         ]
        );
    }
}