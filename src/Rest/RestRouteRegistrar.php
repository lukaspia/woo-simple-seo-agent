<?php

declare(strict_types=1);

namespace WooSimpleSeoAgent\Rest;

/**
 * Class ApiManager
 *
 * @package WooSimpleSeoAgent\Rest
 */
final class RestRouteRegistrar
{
    public const NAMESPACE = 'wssa/v1';

    public function __construct(
        private readonly array $controllers
    ) {}


    /**
     * @return void
     */
    public function registerRoutes(): void
    {
        foreach ($this->controllers as $controller) {
            $controller->registerRoutes(self::NAMESPACE);
        }
    }
}
