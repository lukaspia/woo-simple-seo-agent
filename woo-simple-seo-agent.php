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

use WooSimpleSeoAgent\Assets\AssetManager;
use WooSimpleSeoAgent\Controller\Admin\ProductSeoMetaboxController;
use WooSimpleSeoAgent\Rest\ApiManager;

if (!defined('ABSPATH')) {
    exit;
}

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
    private static ?self $instance = null;

    /**
     * @param array<string, object> $services
     */
    private function __construct(
        private readonly array $services
    ) {}

    public static function instance(): self
    {
        if (self::$instance === null) {
            $assets = new AssetManager(
                plugin_dir_path(__FILE__),
                plugin_dir_url(__FILE__)
            );

            $metabox = new ProductSeoMetaboxController();
            $api     = new ApiManager();

            self::$instance = new self([
                                           'assets'  => $assets,
                                           'metabox' => $metabox,
                                           'api'     => $api,
                                       ]);
        }

        return self::$instance;
    }

    public function getService(string $key): ?object
    {
        return $this->services[$key] ?? null;
    }
}

/**
 * Begins execution of the plugin.
 *
 * @since 1.0.0
 */
add_action('plugins_loaded', function () {
    WooSimpleSeoAgent::instance();
});