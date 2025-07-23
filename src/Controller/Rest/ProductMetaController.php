<?php

declare(strict_types=1);

namespace WooSimpleSeoAgent\Controller\Rest;

use WP_REST_Request;
use WP_REST_Response;

class ProductMetaController implements RestControllerInterface
{
    public function registerRoutes(string $namespace): void
    {
        register_rest_route(
            $namespace,
            '/product/update-title',
            [
                'methods' => 'POST',
                'callback' => [$this, 'updateTitle'],
                'permission_callback' => [$this, 'checkPermissions'],
                'args' => [
                    'product_id' => [
                        'required' => true,
                        'validate_callback' => 'is_numeric',
                        'sanitize_callback' => 'absint',
                    ],
                    'title' => [
                        'required' => true,
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                ],
            ]
        );

        register_rest_route(
            $namespace,
            '/product/update-description',
            [
                'methods' => 'POST',
                'callback' => [$this, 'updateDescription'],
                'permission_callback' => [$this, 'checkPermissions'],
                'args' => [
                    'product_id' => [
                        'required' => true,
                        'validate_callback' => 'is_numeric',
                        'sanitize_callback' => 'absint',
                    ],
                    'description' => [
                        'required' => true,
                        'sanitize_callback' => 'wp_kses_post',
                    ],
                ],
            ]
        );

        register_rest_route(
            $namespace,
            '/product/update-short-description',
            [
                'methods' => 'POST',
                'callback' => [$this, 'updateShortDescription'],
                'permission_callback' => [$this, 'checkPermissions'],
                'args' => [
                    'product_id' => [
                        'required' => true,
                        'validate_callback' => 'is_numeric',
                        'sanitize_callback' => 'absint',
                    ],
                    'short_description' => [
                        'required' => true,
                        'sanitize_callback' => 'wp_kses_post',
                    ],
                ],
            ]
        );

        register_rest_route(
            $namespace,
            '/product/update-keywords',
            [
                'methods' => 'POST',
                'callback' => [$this, 'updateKeywords'],
                'permission_callback' => [$this, 'checkPermissions'],
                'args' => [
                    'product_id' => [
                        'required' => true,
                        'validate_callback' => 'is_numeric',
                        'sanitize_callback' => 'absint',
                    ],
                    'keywords' => [
                        'required' => true,
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                ],
            ]
        );
    }

    public function checkPermissions(): bool
    {
        return current_user_can('edit_products');
    }

    public function updateTitle(WP_REST_Request $request): WP_REST_Response
    {
        $productId = $request->get_param('product_id');
        $title = $request->get_param('title');

        if (!get_post($productId)) {
            return $this->errorResponse('Product not found', 404);
        }

        $result = wp_update_post([
                                     'ID' => $productId,
                                     'post_title' => $title,
                                 ], true);

        if (is_wp_error($result)) {
            return $this->errorResponse($result->get_error_message());
        }

        return new WP_REST_Response([
                                        'success' => true,
                                        'data' => [
                                            'product_id' => $productId,
                                            'title' => $title,
                                        ],
                                        'message' => 'Product title updated successfully',
                                    ]);
    }

    public function updateDescription(WP_REST_Request $request): WP_REST_Response
    {
        $productId = $request->get_param('product_id');
        $description = $request->get_param('description');

        if (!get_post($productId)) {
            return $this->errorResponse('Product not found', 404);
        }

        $result = wp_update_post([
                                     'ID' => $productId,
                                     'post_content' => $description,
                                 ], true);

        if (is_wp_error($result)) {
            return $this->errorResponse($result->get_error_message());
        }

        return new WP_REST_Response([
                                        'success' => true,
                                        'data' => [
                                            'product_id' => $productId,
                                        ],
                                        'message' => 'Product description updated successfully',
                                    ]);
    }

    public function updateShortDescription(WP_REST_Request $request): WP_REST_Response
    {
        $productId = $request->get_param('product_id');
        $shortDescription = $request->get_param('short_description');

        if (!get_post($productId)) {
            return $this->errorResponse('Product not found', 404);
        }

        $result = wp_update_post([
                                     'ID' => $productId,
                                     'post_content' => $shortDescription,
                                 ], true);

        return new WP_REST_Response([
                                        'success' => (bool)$result,
                                        'data' => [
                                            'product_id' => $productId,
                                            'short_description' => $shortDescription,
                                        ],
                                        'message' => $result
                                            ? 'Product short description updated successfully'
                                            : 'Failed to update product short description',
                                    ], $result ? 200 : 500);
    }

    public function updateKeywords(WP_REST_Request $request): WP_REST_Response
    {
        $productId = $request->get_param('product_id');
        $keywords = $request->get_param('keywords');

        if (!get_post($productId)) {
            return $this->errorResponse('Product not found', 404);
        }

        $result = $this->updateWoocommerceProductTags($productId, explode(',', $keywords));

        return new WP_REST_Response([
                                        'success' => (bool)$result,
                                        'data' => [
                                            'product_id' => $productId,
                                            'keywords' => $keywords,
                                        ],
                                        'message' => $result
                                            ? 'Product keywords updated successfully'
                                            : 'Failed to update product keywords',
                                    ], $result ? 200 : 500);
    }

    private function errorResponse(string $message, int $status = 400): WP_REST_Response
    {
        return new WP_REST_Response([
                                        'success' => false,
                                        'message' => $message,
                                    ], $status);
    }

    private function updateWoocommerceProductTags(int $productId, array $newTags): bool
    {
        $result = wp_set_object_terms(
            $productId,
            $newTags,
            'product_tag',
            false
        );

        if (is_wp_error($result)) {
            return false;
        }

        return true;
    }
}