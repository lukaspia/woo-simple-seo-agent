<?php

declare(strict_types=1);

namespace WooSimpleSeoAgent\Controller\Rest;


use WooSimpleSeoAgent\Service\SeoAgentInterface;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WooSimpleSeoAgent\Controller\Rest\RestControllerInterface;

/**
 * Class AgentSeoController
 *
 * @package WooSimpleSeoAgent\Controller\Rest
 */
class AgentSeoController extends AbstractRestController
{
    public const GENERATE_SEO_URL = '/agent/generate';

    public function __construct(
        private readonly SeoAgentInterface $seoAgent
    )
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
            self::GENERATE_SEO_URL,
            [
                'methods' => 'POST',
                'callback' => [$this, 'handleGenerateRequest'],
                'permission_callback' => [$this, 'checkPermissions']
            ]
        );
    }

    /**
     * Handle the request to generate SEO data.
     *
     * @param WP_REST_Request $request The request object.
     *
     * @return WP_REST_Response
     * @throws \Throwable
     */
    public function handleGenerateRequest(WP_REST_Request $request): WP_REST_Response
    {
        $productId = $request->get_param('product_id');

        if (empty($productId) || !is_numeric($productId) || (int)$productId <= 0) {
            return $this->errorResponse(
                __('Invalid product ID', 'woo-simple-seo-agent'),
                400
            );
        }

        $productId = (int)$productId;
        $product = wc_get_product($productId);

        if (!$product) {
            return $this->errorResponse(
                __('Product not found', 'woo-simple-seo-agent'),
                404
            );
        }

        $requestMessage = $request->get_param('request_message');
        $requestMessage = $requestMessage ? sanitize_text_field($requestMessage) : '';

        $prompt = "Need SEO optimization for product id $productId";
        if (!empty($requestMessage)) {
            $prompt .= ". Additional request: $requestMessage";
        }

        $prompt = apply_filters('wssa_agent_prompt', $prompt, $productId);

        $conversationHistory = $request->get_param('conversation_history');

        try {
            $structuredResult = $this->seoAgent->generateSeoContent($prompt, [
                'conversationHistory' => $conversationHistory ?? []
            ]);

            if (empty($structuredResult)) {
                return $this->errorResponse(
                    __('Invalid response format from AI', 'woo-simple-seo-agent'),
                    500
                );
            }

            return $this->successResponse([
                'seoData' => $structuredResult, 
                'prompt' => $prompt
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                500
            );
        }
    }
}
