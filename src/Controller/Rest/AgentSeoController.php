<?php

declare(strict_types=1);

namespace WooSimpleSeoAgent\Controller\Rest;

use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\StructuredOutput\JsonExtractor;
use WooSimpleSeoAgent\Neuron\SeoAgent;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WooSimpleSeoAgent\Controller\Rest\RestControllerInterface;

/**
 * Class AgentSeoController
 *
 * @package WooSimpleSeoAgent\Controller\Rest
 */
readonly class AgentSeoController implements RestControllerInterface
{

    public function __construct(private SeoAgent $seoAgent, private JsonExtractor $jsonExtractor)
    {
    }

    /**
     * Registers the routes for the controller.
     *
     * @param string $namespace The namespace for the routes.
     */
    public function registerRoutes(string $namespace): void
    {
        register_rest_route(
            $namespace,
            '/agent/generate',
            [
                'methods' => 'POST',
                'callback' => [$this, 'handleGenerateRequest'],
                'permission_callback' => '__return_true'
                /*'permission_callback' => static function () {
                    return current_user_can('edit_posts');
                }*/
            ]
        );
    }

    /**
     * Handle the request to generate SEO data.
     *
     * @param WP_REST_Request $request The request object.
     *
     * @return WP_REST_Response|WP_Error
     * @throws \Throwable
     */
    public function handleGenerateRequest(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $productId = $request->get_param('product_id');
        if (empty($productId) || !is_numeric($productId) || (int)$productId <= 0) {
            return new WP_Error(
                'invalid_product_id',
                'A valid Product ID is required.',
                ['status' => 400]
            );
        }

        $requestMessage = $request->get_param('request_message');
        $requestMessage = $requestMessage ? sanitize_text_field($requestMessage) : '';

        $prompt = "Need SEO optimization for product id {$productId}";
        if (!empty($requestMessage)) {
            $prompt .= ". Additional request: {$requestMessage}";
        }

        try {
            $seo = $this->seoAgent->chat(
                new UserMessage($prompt)
            );

            $seoJson = $this->jsonExtractor->getJson($seo->getContent());
            $seoObject = json_decode($seoJson, true, 512, JSON_THROW_ON_ERROR);

            return new WP_REST_Response(['seo' => $seoObject], 200);
        } catch (\JsonException $e) {
            return new WP_Error('json_error', $e->getMessage(), ['status' => 500]);
        }
    }
}
