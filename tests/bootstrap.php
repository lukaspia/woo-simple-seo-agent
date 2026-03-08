<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

if (!function_exists('apply_filters')) {
    function apply_filters(string $tag, mixed $value, ...$args): mixed
    {
        return $value;
    }
}

if (!function_exists('is_wp_error')) {
    function is_wp_error($thing): bool
    {
        return $thing instanceof \WP_Error;
    }
}

if (!class_exists('WP_Error')) {
    class WP_Error
    {
    }
}

if (!function_exists('wc_get_product')) {
    function wc_get_product($id)
    {
        global $mock_wc_product;
        return $mock_wc_product ?? null;
    }
}

if (!function_exists('wp_update_post')) {
    function wp_update_post($data)
    {
        global $mock_wp_update_fail;
        return ($mock_wp_update_fail ?? false) ? new \WP_Error() : 1;
    }
}

if (!function_exists('wp_get_object_terms')) {
    function wp_get_object_terms($id, $taxonomy, $args)
    {
        global $mock_wp_terms;
        return $mock_wp_terms ?? [];
    }
}

if (!class_exists('WP_REST_Request')) {
    class WP_REST_Request {
        private $params = [];
        public function __construct($method = '', $route = '') {}
        public function set_param($key, $val) { $this->params[$key] = $val; }
        public function get_param($key) { return $this->params[$key] ?? null; }
    }
}

if (!class_exists('WP_REST_Response')) {
    class WP_REST_Response {
        private $data; private $status;
        public function __construct($data = null, $status = 200) { $this->data = $data; $this->status = $status; }
        public function get_status() { return $this->status; }
        public function get_data() { return $this->data; }
    }
}

if (!function_exists('__')) {
    function __($text, $domain = 'default') {
        return $text;
    }
}

if (!function_exists('esc_html__')) {
    function esc_html__($text, $domain = 'default') {
        return $text;
    }
}

if (!function_exists('get_post')) {
    function get_post($id) {
        global $mock_post;
        return $mock_post;
    }
}
