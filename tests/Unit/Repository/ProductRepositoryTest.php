<?php

declare(strict_types=1);


namespace WooSimpleSeoAgent\Tests\Unit\Repository;


use PHPUnit\Framework\TestCase;
use WooSimpleSeoAgent\Repository\ProductRepository;

class ProductRepositoryTest extends TestCase
{
    private ProductRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new ProductRepository();
    }

    public function test_get_product_data_for_seo_returns_correct_structure(): void
    {
        $productMock = new class {
            public function get_title() { return 'Testowy Produkt'; }
            public function get_description() { return 'Długi opis'; }
            public function get_short_description() { return 'Krótki opis'; }
        };

        global $mock_wc_product;
        $mock_wc_product = $productMock;

        $result = $this->repository->getProductDataForSeo(123);

        $this->assertIsArray($result);
        $this->assertEquals('Testowy Produkt', $result['title']);
        $this->assertEquals('Długi opis', $result['description']);
        $this->assertEquals('Krótki opis', $result['shortDescription']);
    }

    public function test_update_methods_handle_success(): void
    {
        $this->assertTrue($this->repository->updateTitle(123, 'Nowy Tytuł'));
        $this->assertTrue($this->repository->updateContent(123, 'Nowa Treść'));
    }

    public function test_get_product_tag_names_returns_empty_string_on_no_tags(): void
    {
        $result = $this->repository->getProductTagNames(123);

        $this->assertIsString($result);
        $this->assertEquals('', $result);
    }
}