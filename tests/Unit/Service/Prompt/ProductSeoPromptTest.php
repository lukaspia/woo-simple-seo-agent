<?php

declare(strict_types=1);


namespace WooSimpleSeoAgent\Tests\Unit\Service\Prompt;


use PHPUnit\Framework\TestCase;
use WooSimpleSeoAgent\Service\Prompt\ProductSeoPrompt;

class ProductSeoPromptTest extends TestCase
{
    public function test_to_string_returns_basic_prompt(): void
    {
        $productId = 101;
        $prompt = new ProductSeoPrompt($productId);

        $expected = "Need SEO optimization for product id 101";

        $this->assertEquals($expected, $prompt->toString());
    }

    public function test_to_string_includes_additional_message(): void
    {
        $productId = 202;
        $additionalMessage = "Make it sound more professional";
        $prompt = new ProductSeoPrompt($productId, $additionalMessage);

        $expected = "Need SEO optimization for product id 202. Additional request: Make it sound more professional";

        $this->assertEquals($expected, $prompt->toString());
    }

    public function test_to_string_handles_empty_string_explicitly(): void
    {
        $prompt = new ProductSeoPrompt(303, "");

        $this->assertStringNotContainsString('Additional request:', $prompt->toString());
        $this->assertEquals("Need SEO optimization for product id 303", $prompt->toString());
    }
}