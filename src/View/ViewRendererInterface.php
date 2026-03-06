<?php

namespace WooSimpleSeoAgent\View;

interface ViewRendererInterface
{
    /**
     * @param string $templatePath
     * @param array $data
     * @return void
     */
    public function render(string $templatePath, array $data = []): void;
}