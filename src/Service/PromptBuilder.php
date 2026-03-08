<?php

declare(strict_types=1);


namespace WooSimpleSeoAgent\Service;

use WooSimpleSeoAgent\Service\Prompt\PromptInterface;
use WooSimpleSeoAgent\Service\Prompt\ProductSeoPrompt;

final class PromptBuilder
{
    public function createProductSeoPrompt(int $productId, string $message = ''): PromptInterface
    {
        return new ProductSeoPrompt($productId, $message);
    }
}