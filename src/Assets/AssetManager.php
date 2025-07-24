<?php

namespace WooSimpleSeoAgent\Assets;

use WooSimpleSeoAgent\Controller\Rest\AgentSeoController;
use WooSimpleSeoAgent\Controller\Rest\ProductMetaController;
use WooSimpleSeoAgent\Rest\ApiManager;

readonly class AssetManager
{
    public function __construct(private string $pluginDirPath, private string $pluginDirUrl)
    {
        add_action('admin_enqueue_scripts', [$this, 'registerAdminScripts']);
    }

    public function registerAdminScripts($hookSuffix): void
    {
        global $post;

        if ('post.php' !== $hookSuffix && 'post-new.php' !== $hookSuffix) {
            return;
        }

        if (!$post || 'product' !== $post->post_type) {
            return;
        }

        $scriptPath = $this->pluginDirPath . 'assets/dist/bundle.js';
        $scriptUrl = $this->pluginDirUrl . 'assets/dist/bundle.js';
        $version = file_exists($scriptPath) ? filemtime($scriptPath) : '1.0.0';

        wp_enqueue_script(
            'woo-simple-seo-agent-script',
            $scriptUrl,
            ['jquery'],
            $version,
            true
        );

        $namespace = '/'. ApiManager::NAMESPACE;
        wp_localize_script(
            'woo-simple-seo-agent-script',
            'wssa_params',
            [
                'rest_url'   => esc_url_raw(rest_url($namespace . AgentSeoController::GENERATE_SEO_URL)),
                'rest_product_meta_url' => [
                    'update_title' => esc_url_raw(rest_url($namespace . ProductMetaController::UPDATE_TITLE_URL)),
                    'update_description' => esc_url_raw(rest_url($namespace . ProductMetaController::UPDATE_DESCRIPTION_URL)),
                    'update_short_description' => esc_url_raw(rest_url($namespace . ProductMetaController::UPDATE_SHORT_DESCRIPTION_URL)),
                    'update_keywords' => esc_url_raw(rest_url($namespace . ProductMetaController::UPDATE_KEYWORDS_URL)),
                ],
                'nonce'      => wp_create_nonce('wp_rest'),
                'product_id' => get_the_ID(),
            ]
        );
    }
}
