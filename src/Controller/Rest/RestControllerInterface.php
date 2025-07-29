<?php

declare(strict_types=1);

namespace WooSimpleSeoAgent\Controller\Rest;

/**
 * Interface RestControllerInterface
 *
 * @package WooSimpleSeoAgent\Controller\Rest
 */
interface RestControllerInterface
{
    /**
     * Registers the routes for the controller.
     *
     * @param string $namespace The namespace for the routes.
     */
    public function registerRoutes(string $namespace): void;
}
