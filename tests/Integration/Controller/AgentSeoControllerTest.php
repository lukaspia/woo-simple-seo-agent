<?php

declare(strict_types=1);


namespace WooSimpleSeoAgent\Tests\Integration\Controller;

use PHPUnit\Framework\TestCase;
use WooSimpleSeoAgent\Controller\Rest\AgentSeoController;
use WooSimpleSeoAgent\Service\SeoAgentInterface;
use WooSimpleSeoAgent\Service\PromptBuilderInterface;
use WooSimpleSeoAgent\Service\Prompt\ProductSeoPrompt;
use WooSimpleSeoAgent\Dto\SeoDto;

class AgentSeoControllerTest extends TestCase
{
    private $agentMock;
    private $promptBuilderMock;
    private $controller;
    private $namespace = 'woo-simple-seo-agent/v1';

    protected function setUp(): void
    {
        parent::setUp();
        $this->agentMock = $this->createMock(SeoAgentInterface::class);
        $this->promptBuilderMock = $this->createMock(PromptBuilderInterface::class);
        $this->controller = new AgentSeoController($this->agentMock, $this->promptBuilderMock);
        global $mock_wc_product;
        $mock_wc_product = null;
    }

    public function test_handle_generate_request_success(): void
    {
        $productId = 123;
        global $mock_wc_product;
        $mock_wc_product = new \stdClass();
        $promptObject = new ProductSeoPrompt($productId, 'test');
        $seoDto = new SeoDto(title: 'AI Title', description: 'AI Desc');

        $this->promptBuilderMock->method('createProductSeoPrompt')->willReturn($promptObject);
        $this->agentMock->method('generateSeoContent')->willReturn($seoDto);

        $request = new \WP_REST_Request('POST', $this->namespace . '/agent/generate');
        $request->set_param('product_id', $productId);

        $response = $this->controller->handleGenerateRequest($request);
        $this->assertEquals(200, $response->get_status());
        $data = $response->get_data();
        $this->assertTrue($data['success']);
    }

    public function test_handle_generate_request_returns_404_if_product_missing(): void
    {
        global $mock_wc_product;
        $mock_wc_product = null;

        $request = new \WP_REST_Request('POST', $this->namespace . '/agent/generate');
        $request->set_param('product_id', 999);

        $response = $this->controller->handleGenerateRequest($request);
        $this->assertEquals(404, $response->get_status());
        $this->assertFalse($response->get_data()['success']);
    }

    public function test_handle_generate_request_returns_500_on_agent_error(): void
    {
        global $mock_wc_product;
        $mock_wc_product = new \stdClass();

        $this->promptBuilderMock->method('createProductSeoPrompt')
            ->willReturn(new ProductSeoPrompt(123));

        $this->agentMock->method('generateSeoContent')
            ->willThrowException(new \Exception("Błąd połączenia z modelem AI"));

        $request = new \WP_REST_Request('POST', $this->namespace . '/agent/generate');
        $request->set_param('product_id', 123);

        $response = $this->controller->handleGenerateRequest($request);

        $this->assertEquals(500, $response->get_status());

        $responseData = $response->get_data();
        $message = $responseData['data']['message'] ?? $responseData['message'] ?? null;

        $this->assertNotNull($message, "Nie znaleziono klucza z wiadomością o błędzie w odpowiedzi JSON");
        $this->assertStringContainsString('Błąd połączenia z modelem AI', $message);
    }
}