<?php

declare(strict_types=1);

namespace WooSimpleSeoAgent\Rest;

use NeuronAI\StructuredOutput\JsonExtractor;
use WooSimpleSeoAgent\Controller\Rest\AgentSeoController;
use WooSimpleSeoAgent\Controller\Rest\ProductMetaController;
use WooSimpleSeoAgent\Neuron\SeoAgent;

/**
 * Class ApiManager
 *
 * @package WooSimpleSeoAgent\Rest
 */
class ApiManager
{
    private const NAMESPACE = 'wssa/v1';

    /**
     * Initialize the API manager.
     */
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'registerRoutes']);
    }

    /**
     * Register all REST routes.
     */
    public function registerRoutes(): void
    {
        foreach ($this->getControllers() as $controller) {
            $controller->registerRoutes(self::NAMESPACE);
        }
    }

    /**
     * Get all REST controllers.
     *
     * @return array
     */
    private function getControllers(): array
    {
        return [
            new AgentSeoController(
                new SeoAgent(),
                new JsonExtractor()
            ),
            new ProductMetaController()
        ];
    }
}
