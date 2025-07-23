<?php

declare(strict_types=1);

namespace WooSimpleSeoAgent\Controller\Rest;

use WP_REST_Request;
use WP_REST_Response;

/**
 * Abstract base controller for REST endpoints
 */
abstract class AbstractRestController implements RestControllerInterface
{
    /**
     * Check if current user has permission to access the endpoint
     * 
     * @return bool
     */
    public function checkPermissions(): bool
    {
        return current_user_can('edit_posts');
    }

    /**
     * Create a standardized error response
     * 
     * @param string $message Error message
     * @param int $status HTTP status code
     * @return WP_REST_Response
     */
    protected function errorResponse(string $message, int $status = 400): WP_REST_Response
    {
        return new WP_REST_Response(
            [
                'success' => false,
                'message' => $message,
            ],
            $status
        );
    }

    /**
     * Create a standardized success response
     * 
     * @param array $data Response data
     * @param string $message Success message
     * @param int $status HTTP status code
     * @return WP_REST_Response
     */
    protected function successResponse(array $data = [], string $message = '', int $status = 200): WP_REST_Response
    {
        return new WP_REST_Response(
            [
                'success' => true,
                'data' => $data,
                'message' => $message,
            ],
            $status
        );
    }
}
