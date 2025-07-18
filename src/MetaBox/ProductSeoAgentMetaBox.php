<?php
declare(strict_types=1);

namespace WooSimpleSeoAgent\MetaBox;

/**
 * Class ProductMetaBox
 * 
 * @package WooSimpleSeoAgent
 */
class ProductSeoAgentMetaBox
{
    /**
     * ProductMetaBox constructor.
     */
    public function __construct()
    {
        add_action('add_meta_boxes', [$this, 'register']);
        add_action('save_post_product', [$this, 'save']);
    }

    /**
     * Register the meta box.
     */
    public function register(): void
    {
        add_meta_box(
            'wssa_product_seo_meta_box',
            __('Simple SEO agent', 'woo-simple-seo-agent'),
            [$this, 'render'],
            'product',
            'normal',
            'high'
        );
    }

    /**
     * Render the meta box.
     *
     * @param \WP_Post $post
     */
    public function render(\WP_Post $post): void
    {
        $seoMetaTag = get_post_meta($post->ID, '_wssa_seo_meta_tag', true);

        $templatePath = plugin_dir_path(__DIR__) . '../templates/product-seo-metabox-form.php';

        if (file_exists($templatePath)) {
            include $templatePath;
        }
    }

    /**
     * Save the meta box data.
     *
     * @param int $post_id
     */
    public function save(int $post_id): void
    {

    }
}
