<?php

declare(strict_types=1);

namespace WooSimpleSeoAgent\Controller\Admin;

use WooSimpleSeoAgent\View\ViewRendererInterface;

final class ProductSeoMetaboxController
{
    private const METABOX_ID = 'woo_simple_seo_agent_metabox';

    /**
     * @param string $templatePath
     * @param \WooSimpleSeoAgent\View\ViewRendererInterface $renderer
     */
    public function __construct(
        private readonly string $templatePath,
        private readonly ViewRendererInterface $renderer
    ) {
    }

    /**
     * @return void
     */
    public function register(): void
    {
        add_meta_box(
            self::METABOX_ID,
            __('Woo Simple SEO Agent', 'woo-simple-seo-agent'),
            [$this, 'render'],
            'product',
            'normal',
            'high'
        );
    }

    /**
     * @param \WP_Post $post
     * @return void
     */
    public function render(\WP_Post $post): void
    {
        $this->renderer->render(
            $this->templatePath . 'product-seo-metabox-form.php',
            [
                'productId' => $post->ID,
                'nonce' => wp_create_nonce('wp_rest'),
            ]
        );
    }
}
