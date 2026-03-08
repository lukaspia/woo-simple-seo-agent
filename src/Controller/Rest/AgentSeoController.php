<?php

declare(strict_types=1);

namespace WooSimpleSeoAgent\Controller\Rest;


use WooSimpleSeoAgent\Service\PromptBuilderInterface;
use WooSimpleSeoAgent\Service\SeoAgentInterface;
use WP_REST_Request;
use WP_REST_Response;

final class AgentSeoController extends AbstractRestController implements RestControllerInterface
{
    public const ROUTE_GENERATE = '/agent/generate';

    public function __construct(
        private readonly SeoAgentInterface $seoAgent,
        private readonly PromptBuilderInterface $promptBuilder
    ) {
    }

    public function registerRoutes(string $namespace): void
    {
        register_rest_route($namespace, self::ROUTE_GENERATE, [
            'methods' => 'POST',
            'callback' => [$this, 'handleGenerateRequest'],
            'permission_callback' => [$this, 'checkPermissions'],
            'args' => $this->getRouteArgs(),
        ]);
    }

    public function handleGenerateRequest(WP_REST_Request $request): WP_REST_Response
    {
        $productId = (int)$request->get_param('product_id');

        if (!wc_get_product($productId)) {
            return $this->errorResponse(__('Product not found', 'woo-simple-seo-agent'), 404);
        }

        try {
            $promptObject = $this->promptBuilder->createProductSeoPrompt(
                $productId,
                $request->get_param('request_message') ?? ''
            );

            $result = $this->seoAgent->generateSeoContent($promptObject->toString(), [
                'conversationHistory' => $request->get_param('conversation_history') ?? []
            ]);

            return $this->successResponse([
                                              'seoData' => $result->toArray(),
                                              'prompt' => $promptObject->toString()
                                          ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    private function getRouteArgs(): array
    {
        return [
            'product_id' => [
                'required' => true,
                'validate_callback' => fn($val) => is_numeric($val) && (int)$val > 0,
                'sanitize_callback' => 'absint',
            ],
            'request_message' => [
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'conversation_history' => [
                'default' => [],
                'validate_callback' => fn($val) => is_array($val),
            ],
        ];
    }
}
