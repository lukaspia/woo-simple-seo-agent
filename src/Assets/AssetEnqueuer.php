<?php

declare(strict_types=1);

namespace WooSimpleSeoAgent\Assets;

use WooSimpleSeoAgent\Controller\Rest\AgentSeoController;
use WooSimpleSeoAgent\Controller\Rest\ProductMetaController;
use WooSimpleSeoAgent\Rest\RestRouteRegistrar;

final readonly class AssetEnqueuer
{
    private const SCRIPT_HANDLE = 'woo-simple-seo-agent-script';
    private const STYLE_HANDLE = 'woo-simple-seo-agent-admin-style';

    public function __construct(
        private string $pluginDirPath,
        private string $pluginDirUrl
    ) {
    }

    /**
     * @param string $hookSuffix
     * @return void
     */
    public function registerAdminScripts(string $hookSuffix): void
    {
        if (!in_array($hookSuffix, ['post.php', 'post-new.php'], true)) {
            return;
        }

        $screen = get_current_screen();
        if (!$screen || $screen->post_type !== 'product') {
            return;
        }

        $this->enqueueStyles();
        $this->enqueueScripts();
        $this->localizeScripts();
    }

    /**
     * @return void
     */
    private function enqueueScripts(): void
    {
        $path = 'assets/dist/js/main.js';
        wp_enqueue_script(
            self::SCRIPT_HANDLE,
            $this->pluginDirUrl . $path,
            ['jquery'],
            $this->getAssetVersion($path),
            true
        );
    }

    /**
     * @return void
     */
    private function enqueueStyles(): void
    {
        $path = 'assets/dist/css/admin.css';
        wp_enqueue_style(
            self::STYLE_HANDLE,
            $this->pluginDirUrl . $path,
            [],
            $this->getAssetVersion($path)
        );
    }

    /**
     * @return void
     */
    private function localizeScripts(): void
    {
        $namespace = RestRouteRegistrar::NAMESPACE;

        wp_localize_script(
            self::SCRIPT_HANDLE,
            'wssa_params',
            [
                'rest_url' => esc_url_raw(rest_url($namespace . AgentSeoController::ROUTE_GENERATE)),
                'rest_product_meta_url' => [
                    'update_title' => esc_url_raw(rest_url($namespace . ProductMetaController::UPDATE_TITLE_URL)),
                    'update_description' => esc_url_raw(
                        rest_url($namespace . ProductMetaController::UPDATE_DESCRIPTION_URL)
                    ),
                    'update_short_description' => esc_url_raw(
                        rest_url($namespace . ProductMetaController::UPDATE_SHORT_DESCRIPTION_URL)
                    ),
                    'update_keywords' => esc_url_raw(rest_url($namespace . ProductMetaController::UPDATE_KEYWORDS_URL)),
                ],
                'nonce' => wp_create_nonce('wp_rest'),
                'product_id' => get_the_ID(),
            ]
        );
    }

    /**
     * @param string $relativePath
     * @return string
     */
    private function getAssetVersion(string $relativePath): string
    {
        $fullPath = $this->pluginDirPath . $relativePath;
        return file_exists($fullPath) ? (string)filemtime($fullPath) : '1.0.0';
    }
}
