<?php

declare(strict_types=1);

namespace WooSimpleSeoAgent;

use WooSimpleSeoAgent\Assets\AssetManager;
use WooSimpleSeoAgent\Controller\Admin\ProductSeoMetaboxController;
use WooSimpleSeoAgent\Rest\ApiManager;
use WooSimpleSeoAgent\View\ViewRenderer;

final class ContainerBuilder
{
    /**
     * @param string $mainFile
     * @return \WooSimpleSeoAgent\ServiceContainer
     */
    public static function build(string $mainFile): ServiceContainer
    {
        $basePath = plugin_dir_path($mainFile);
        $baseUrl = plugin_dir_url($mainFile);

        $renderer = new ViewRenderer();
        $assets = new AssetManager($basePath, $baseUrl);

        $metabox = new ProductSeoMetaboxController(
            $basePath . 'templates/',
            $renderer
        );
        $api = new ApiManager();

        return new ServiceContainer(
            services: [
                          'renderer' => $renderer,
                          'assets'   => $assets,
                      ],
            controllers: [
                          'metabox' => $metabox,
                          'api'     => $api,
                      ]
        );
    }
}