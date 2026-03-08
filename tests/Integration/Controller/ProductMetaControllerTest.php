<?php

declare(strict_types=1);


namespace WooSimpleSeoAgent\Tests\Integration\Controller;

use PHPUnit\Framework\TestCase;
use WooSimpleSeoAgent\Controller\Rest\ProductMetaController;
use WooSimpleSeoAgent\Repository\ProductRepositoryInterface;
use WP_REST_Request;

class ProductMetaControllerTest extends TestCase
{
    private $repositoryMock;
    private $controller;
    private $namespace = 'woo-simple-seo-agent/v1';

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = $this->createMock(ProductRepositoryInterface::class);
        $this->controller = new ProductMetaController($this->repositoryMock);

        global $mock_post;
        $mock_post = null;
    }

    /**
     * Pomocnicza metoda do wywoływania prywatnych metod kontrolera.
     */
    private function invokeHandleUpdate(WP_REST_Request $request, string $field)
    {
        $reflection = new \ReflectionClass(get_class($this->controller));
        $method = $reflection->getMethod('handleUpdate');
        $method->setAccessible(true);

        return $method->invokeArgs($this->controller, [$request, $field]);
    }

    public function test_handle_update_title_success(): void
    {
        $productId = 50;
        $newValue = 'Nowy Tytuł Produktu';

        global $mock_post;
        $mock_post = new \stdClass();
        $mock_post->ID = $productId;

        $this->repositoryMock->expects($this->once())
            ->method('updateTitle')
            ->with($productId, $newValue)
            ->willReturn(true);

        $request = new \WP_REST_Request('POST', $this->namespace . '/product/update-title');
        $request->set_param('product_id', $productId);
        $request->set_param('value', $newValue);

        $response = $this->invokeHandleUpdate($request, 'title');

        $this->assertEquals(200, $response->get_status());
        $this->assertTrue($response->get_data()['success']);
        $this->assertEquals($newValue, $response->get_data()['data']['title']);
    }

    public function test_handle_update_keywords_explodes_string(): void
    {
        $productId = 51;
        $keywordsString = 'tag1,tag2,tag3';

        global $mock_post;
        $mock_post = new \stdClass();

        $this->repositoryMock->expects($this->once())
            ->method('updateTags')
            ->with($productId, ['tag1', 'tag2', 'tag3'])
            ->willReturn(true);

        $request = new \WP_REST_Request('POST', $this->namespace . '/product/update-keywords');
        $request->set_param('product_id', $productId);
        $request->set_param('value', $keywordsString);

        $response = $this->invokeHandleUpdate($request, 'keywords');

        $this->assertEquals(200, $response->get_status());
    }

    public function test_handle_update_returns_404_if_post_missing(): void
    {
        global $mock_post;
        $mock_post = null;

        $request = new \WP_REST_Request('POST', $this->namespace . '/product/update-title');
        $request->set_param('product_id', 999);
        $request->set_param('value', 'some value');

        $response = $this->invokeHandleUpdate($request, 'title');

        $this->assertEquals(404, $response->get_status());
        $this->assertFalse($response->get_data()['success']);
    }

    public function test_handle_update_returns_error_on_repository_failure(): void
    {
        global $mock_post;
        $mock_post = new \stdClass();

        // Symulujemy, że repozytorium zawiodło
        $this->repositoryMock->method('updateContent')->willReturn(false);

        $request = new \WP_REST_Request('POST', $this->namespace . '/product/update-description');
        $request->set_param('product_id', 50);
        $request->set_param('value', 'lorem ipsum');

        $response = $this->invokeHandleUpdate($request, 'description');
        $responseData = $response->get_data();

        $this->assertFalse($responseData['success']);

        // Sprawdzamy różne możliwe lokalizacje wiadomości błędu
        $message = $responseData['data']['message'] ?? $responseData['message'] ?? $responseData['error'] ?? null;

        $this->assertEquals('Failed to update product field', $message);
    }
}