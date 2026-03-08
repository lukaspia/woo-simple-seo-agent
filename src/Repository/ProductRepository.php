<?php

declare(strict_types=1);


namespace WooSimpleSeoAgent\Repository;


final class ProductRepository implements ProductRepositoryInterface
{
    /**
     * @param int $productId
     * @param string $title
     * @return bool
     */
    public function updateTitle(int $productId, string $title): bool
    {
        return !is_wp_error(wp_update_post(['ID' => $productId, 'post_title' => $title]));
    }

    /**
     * @param int $productId
     * @param string $content
     * @return bool
     */
    public function updateContent(int $productId, string $content): bool
    {
        return !is_wp_error(wp_update_post(['ID' => $productId, 'post_content' => $content]));
    }

    /**
     * @param int $productId
     * @param string $excerpt
     * @return bool
     */
    public function updateExcerpt(int $productId, string $excerpt): bool
    {
        return !is_wp_error(wp_update_post(['ID' => $productId, 'post_excerpt' => $excerpt]));
    }

    /**
     * @param int $productId
     * @param array $tags
     * @return bool
     */
    public function updateTags(int $productId, array $tags): bool
    {
        return !is_wp_error(wp_set_object_terms($productId, $tags, 'product_tag', false));
    }

    /**
     * @param int $productId
     * @return string
     */
    public function getProductTagNames(int $productId): string
    {
        $tags = wp_get_object_terms($productId, 'product_tag', ['fields' => 'names']);

        if (is_wp_error($tags) || empty($tags)) {
            return '';
        }

        return implode(', ', $tags);
    }

    /**
     * @param int $productId
     * @return array
     */
    public function getProductDataForSeo(int $productId): array
    {
        $product = wc_get_product($productId);
        if (!$product) {
            return [];
        }

        return [
            'title' => $product->get_title(),
            'description' => $product->get_description(),
            'shortDescription' => $product->get_short_description(),
            'keywords' => $this->getProductTagNames($productId),
        ];
    }
}