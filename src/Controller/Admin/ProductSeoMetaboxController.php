<?php

declare(strict_types=1);

namespace WooSimpleSeoAgent\Controller\Admin;

class ProductSeoMetaboxController
{
    public function __construct()
    {
        add_action('add_meta_boxes', [$this, 'addSeoMetabox']);
    }

    public function addSeoMetabox(): void
    {
        add_meta_box(
            'woo_simple_seo_agent_metabox',
            __('Woo Simple SEO Agent', 'woo-simple-seo-agent'),
            [$this, 'renderSeoMetabox'],
            'product',
            'normal',
            'high'
        );
    }

    public function renderSeoMetabox(\WP_Post $post): void
    {
        $templatePath = plugin_dir_path(__DIR__) . '../../templates/product-seo-metabox-form.php';

        if (file_exists($templatePath)) {
            include $templatePath;
        }
    }
}
