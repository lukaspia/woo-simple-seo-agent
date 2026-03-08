<?php

namespace WooSimpleSeoAgent\Repository;

interface ProductRepositoryInterface
{
    /**
     * @param int $productId
     * @param string $title
     * @return bool
     */
    public function updateTitle(int $productId, string $title): bool;

    /**
     * @param int $productId
     * @param string $content
     * @return bool
     */
    public function updateContent(int $productId, string $content): bool;

    /**
     * @param int $productId
     * @param string $excerpt
     * @return bool
     */
    public function updateExcerpt(int $productId, string $excerpt): bool;

    /**
     * @param int $productId
     * @param array $tags
     * @return bool
     */
    public function updateTags(int $productId, array $tags): bool;

    public function getProductDataForSeo(int $productId): array;
}