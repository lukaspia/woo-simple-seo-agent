<?php

declare(strict_types=1);

if (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    require_once dirname(__DIR__) . '/vendor/autoload.php';
}

if (!defined('WP_TESTS_DIR')) {
    define('WP_TESTS_DIR', dirname(__DIR__) . '/../../../../wordpress-develop/tests/phpunit/');
}

if (file_exists(WP_TESTS_DIR . 'includes/functions.php')) {
    require_once WP_TESTS_DIR . 'includes/functions.php';

    tests_add_filter('muplugins_loaded', function() {
        require_once dirname(__DIR__) . '/woo-simple-seo-agent.php';
    });

    require WP_TESTS_DIR . 'includes/bootstrap.php';
}
