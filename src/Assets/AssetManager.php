<?php

namespace WooSimpleSeoAgent\Assets;

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

        wp_localize_script(
            'woo-simple-seo-agent-script',
            'wssa_params',
            [
                'rest_url'   => esc_url_raw(rest_url('/wssa/v1/agent/generate')),
                'nonce'      => wp_create_nonce('wp_rest'),
                'product_id' => get_the_ID(),
            ]
        );
    }
}
