<?php

declare(strict_types=1);


namespace WooSimpleSeoAgent\Repository;


final class ProductRepository implements ProductRepositoryInterface
{
    public function updateTitle(int $productId, string $title): bool
    {
        return !is_wp_error(wp_update_post(['ID' => $productId, 'post_title' => $title]));
    }

    public function updateContent(int $productId, string $content): bool
    {
        return !is_wp_error(wp_update_post(['ID' => $productId, 'post_content' => $content]));
    }

    public function updateExcerpt(int $productId, string $excerpt): bool
    {
        return !is_wp_error(wp_update_post(['ID' => $productId, 'post_excerpt' => $excerpt]));
    }

    public function updateTags(int $productId, array $tags): bool
    {
        return !is_wp_error(wp_set_object_terms($productId, $tags, 'product_tag', false));
    }
}