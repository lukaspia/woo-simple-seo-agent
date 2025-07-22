<?php
/**
 * Woo simple SEO agent
 *
 * @package     WooSimpleSeoAgent
 * @author      Lukasz Piasny
 *
 * @wordpress-plugin
 * Plugin Name: Woo simple SEO agent
 * Description: SEO support agent for woocommerce.
 * Version: 1.0.0
 * Requires at least: 6.8
 * Tested up to: 6.8.0
 * Requires PHP: 8.1.0
 * Author: Lukasz Piasny
 * Author URI: https://github.com/lukaspia/woo-simple-seo-agent
 * Text Domain: woo-simple-seo-agent
 */

declare(strict_types=1);

namespace WooSimpleSeoAgent;

use WooSimpleSeoAgent\MetaBox\ProductSeoAgentMetaBox;
use WooSimpleSeoAgent\Rest\ApiManager;

if (!defined('ABSPATH')) {
    exit;
}

// Require the autoloader if it exists.
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * The main plugin class.
 *
 * @since 1.0.0
 */
final class WooSimpleSeoAgent
{
    /**
     * The single instance of the class.
     *
     * @var WooSimpleSeoAgent|null
     */
    private static ?WooSimpleSeoAgent $instance = null;

    /**
     * Plugin constructor.
     *
     * Private to prevent direct instantiation.
     *
     * @since 1.0.0
     */
    private function __construct()
    {
        $this->initializeComponents();
    }

    /**
     * Initialize plugin components.
     *
     * @since 1.0.0
     */
    private function initializeComponents(): void
    {
        new ProductSeoAgentMetaBox();
        new ApiManager();
    }

    /**
     * Get the singleton instance of the class.
     *
     * @since 1.0.0
     * @return WooSimpleSeoAgent
     */
    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

/**
 * Begins execution of the plugin.
 *
 * @since 1.0.0
 */
function runWooSimpleSeoAgent(): void
{
    WooSimpleSeoAgent::instance();
}

add_action('plugins_loaded', __NAMESPACE__ . '\\runWooSimpleSeoAgent');
