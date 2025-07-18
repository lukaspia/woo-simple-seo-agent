<?php

declare(strict_types=1);

namespace WooSimpleSeoAgent\Controller\Rest;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WooSimpleSeoAgent\Controller\Rest\RestControllerInterface;

/**
 * Class AgentSeoController
 *
 * @package WooSimpleSeoAgent\Controller\Rest
 */
class AgentSeoController implements RestControllerInterface
{
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
                'methods'             => 'POST',
                'callback'            => [$this, 'handleGenerateRequest'],
                /*'permission_callback' => static function () { //TODO odkomentować
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
     */
    public function handleGenerateRequest(WP_REST_Request $request): WP_REST_Response|WP_Error
    {

        return new WP_REST_Response(['message' => 'Request received, processing will be implemented later.'], 200);
    }
}
