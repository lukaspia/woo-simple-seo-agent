<?php

declare(strict_types=1);

namespace WooSimpleSeoAgent;

final class ServiceContainer
{
    /**
     * @param array $services
     * @param array $controllers
     */
    public function __construct(
        private readonly array $services,
        private readonly array $controllers
    ) {
    }

    /**
     * @param string $key
     * @return object|null
     */
    public function getService(string $key): ?object
    {
        return $this->services[$key] ?? null;
    }

    /**
     * @param string $key
     * @return object|null
     */
    public function getController(string $key): ?object
    {
        return $this->controllers[$key] ?? null;
    }

    /**
     * @return array
     */
    public function getHookables(): array
    {
        return $this->controllers;
    }
}