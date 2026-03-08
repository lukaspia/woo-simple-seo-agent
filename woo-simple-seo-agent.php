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

use WooSimpleSeoAgent\Assets\AssetEnqueuer;
use WooSimpleSeoAgent\Controller\Admin\ProductSeoMetaboxController;
use WooSimpleSeoAgent\Rest\RestRouteRegistrar;

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
        private readonly ServiceContainer $container
    ) {
    }

    /**
     * @return self
     */
    public static function instance(): self
    {
        if (self::$instance === null) {
            $services = ContainerBuilder::build(__FILE__);

            self::$instance = new self($services);
            self::$instance->registerHooks();
        }

        return self::$instance;
    }

    /**
     * @return void
     */
    private function registerHooks(): void
    {
        $assets = $this->container->getService('assets');
        if ($assets instanceof AssetEnqueuer) {
            add_action('admin_enqueue_scripts', [$assets, 'registerAdminScripts']);
        }

        $metabox = $this->container->getController('metabox');
        if ($metabox instanceof ProductSeoMetaboxController) {
            add_action('add_meta_boxes', [$metabox, 'register']);
        }

        $api = $this->container->getController('api');
        if ($api instanceof RestRouteRegistrar) {
            add_action('rest_api_init', [$api, 'registerRoutes']);
        }
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