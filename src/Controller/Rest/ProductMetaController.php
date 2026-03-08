<?php

declare(strict_types=1);

namespace WooSimpleSeoAgent\Controller\Rest;

use WooSimpleSeoAgent\Repository\ProductRepositoryInterface;
use WP_REST_Request;
use WP_REST_Response;

final class ProductMetaController extends AbstractRestController
{
    public const UPDATE_TITLE_URL = '/product/update-title';
    public const UPDATE_DESCRIPTION_URL = '/product/update-description';
    public const UPDATE_SHORT_DESCRIPTION_URL = '/product/update-short-description';
    public const UPDATE_KEYWORDS_URL = '/product/update-keywords';

    private const FIELDS_MAP = [
        self::UPDATE_TITLE_URL => 'title',
        self::UPDATE_DESCRIPTION_URL => 'description',
        self::UPDATE_SHORT_DESCRIPTION_URL => 'short_description',
        self::UPDATE_KEYWORDS_URL => 'keywords',
    ];

    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }

    /**
     * @param string $namespace
     * @return void
     */
    public function registerRoutes(string $namespace): void
    {
        foreach (self::FIELDS_MAP as $route => $field) {
            register_rest_route($namespace, $route, [
                'methods' => 'POST',
                'callback' => fn(WP_REST_Request $request) => $this->handleUpdate($request, $field),
                'permission_callback' => [$this, 'checkPermissions'],
                'args' => $this->getCommonArgs($field),
            ]);
        }
    }

    /**
     * @param \WP_REST_Request $request
     * @param string $field
     * @return \WP_REST_Response
     */
    private function handleUpdate(WP_REST_Request $request, string $field): WP_REST_Response
    {
        $productId = (int)$request->get_param('product_id');
        $value = $request->get_param('value');

        if (!get_post($productId)) {
            return $this->errorResponse(__('Product not found', 'woo-simple-seo-agent'), 404);
        }

        $success = match ($field) {
            'title' => $this->productRepository->updateTitle($productId, (string)$value),
            'description' => $this->productRepository->updateContent($productId, (string)$value),
            'short_description' => $this->productRepository->updateExcerpt($productId, (string)$value),
            'keywords' => $this->productRepository->updateTags($productId, explode(',', (string)$value)),
            default => false,
        };

        if (!$success) {
            return $this->errorResponse(__('Failed to update product field', 'woo-simple-seo-agent'));
        }

        return $this->successResponse(
            ['product_id' => $productId, $field => $value],
            __('Field updated successfully', 'woo-simple-seo-agent')
        );
    }

    /**
     * @param string $field
     * @return array[]
     */
    private function getCommonArgs(string $field): array
    {
        return [
            'product_id' => [
                'required' => true,
                'sanitize_callback' => 'absint',
            ],
            'value' => [
                'required' => true,
                'sanitize_callback' => in_array($field, ['description', 'short_description'])
                    ? 'wp_kses_post'
                    : 'sanitize_text_field',
            ],
        ];
    }
}