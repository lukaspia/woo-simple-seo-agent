<?php

namespace WooSimpleSeoAgent\Service;

use WooSimpleSeoAgent\Service\Prompt\PromptInterface;

interface PromptBuilderInterface
{
    public function createProductSeoPrompt(int $productId, string $additionalMessage = ''): PromptInterface;
}