<?php

declare(strict_types=1);


namespace WooSimpleSeoAgent\Service\Prompt;


use WooSimpleSeoAgent\Service\Prompt\PromptInterface;

final readonly class ProductSeoPrompt implements PromptInterface
{
    public function __construct(
        private int $productId,
        private string $additionalMessage = ''
    ) {
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        $prompt = "Need SEO optimization for product id {$this->productId}";

        if ($this->additionalMessage !== '') {
            $prompt .= ". Additional request: {$this->additionalMessage}";
        }

        return (string)apply_filters('wssa_product_seo_prompt', $prompt, $this->productId);
    }
}