<?php

declare(strict_types=1);


namespace WooSimpleSeoAgent\View;


final class ViewRenderer implements ViewRendererInterface
{
    /**
     * @param string $templatePath
     * @param array $data
     * @return void
     */
    public function render(string $templatePath, array $data = []): void
    {
        if (!file_exists($templatePath)) {
            $this->handleMissing($templatePath);
            return;
        }

        (static function (string $path, array $data) {
            extract($data);
            include $path;
        })(
            $templatePath,
            $data
        );
    }

    /**
     * @param string $path
     * @return void
     */
    private function handleMissing(string $path): void
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("Template not found: $path");
        }
        echo '<div class="error">Template missing.</div>';
    }
}