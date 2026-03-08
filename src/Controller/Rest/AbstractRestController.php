<?php

declare(strict_types=1);

namespace WooSimpleSeoAgent\Controller\Rest;

use WP_REST_Response;
use WP_REST_Request;

abstract class AbstractRestController implements RestControllerInterface
{
    public function checkPermissions(): bool
    {
        return current_user_can('edit_posts');
    }

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
