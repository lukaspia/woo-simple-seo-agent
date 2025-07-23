<?php

declare(strict_types=1);

namespace WooSimpleSeoAgent\Controller\Rest;

use WP_REST_Request;
use WP_REST_Response;

class ProductMetaController extends AbstractRestController
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

        return $this->successResponse(
            [
                'product_id' => $productId,
                'title' => $title,
            ],
            __('Product title updated successfully', 'woo-simple-seo-agent')
        );
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

        return $this->successResponse(
            [
                'product_id' => $productId,
                'description' => $description
            ],
            __('Product description updated successfully', 'woo-simple-seo-agent')
        );
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

        if (is_wp_error($result)) {
            return $this->errorResponse($result->get_error_message());
        }

        return $this->successResponse(
            [
                'product_id' => $productId,
                'short_description' => $shortDescription,
            ],
            __('Product short description updated successfully', 'woo-simple-seo-agent')
        );
    }

    public function updateKeywords(WP_REST_Request $request): WP_REST_Response
    {
        $productId = $request->get_param('product_id');
        $keywords = $request->get_param('keywords');

        if (!get_post($productId)) {
            return $this->errorResponse('Product not found', 404);
        }

        $result = $this->updateWoocommerceProductTags($productId, explode(',', $keywords));

        if (!$result) {
            return $this->errorResponse('Failed to update product keywords');
        }

        return $this->successResponse(
            [
                'product_id' => $productId,
                'keywords' => $keywords,
            ],
            __('Product keywords updated successfully', 'woo-simple-seo-agent')
        );
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