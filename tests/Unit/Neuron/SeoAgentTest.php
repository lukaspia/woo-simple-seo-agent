<?php

namespace WooSimpleSeoAgent\Tests\Unit\Neuron;

use PHPUnit\Framework\TestCase;
use WooSimpleSeoAgent\Neuron\SeoAgent;
use ReflectionClass;
use WooSimpleSeoAgent\Repository\ProductRepositoryInterface;

class SeoAgentTest extends TestCase
{
    private $repositoryMock;
    private SeoAgent $agent;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(ProductRepositoryInterface::class);

        $this->agent = new SeoAgent(
            apiKey: 'test-key',
            model:  'gemini-1.5-flash',
            locale: 'pl_PL',
            productRepository: $this->repositoryMock
        );
    }

    public function test_instructions_returns_formatted_string(): void
    {
        $instructions = $this->agent->instructions();

        $this->assertStringContainsString('pl_PL', $instructions);
        $this->assertStringContainsString('GEO', $instructions);
        $this->assertStringContainsString('json', $instructions);
    }

    public function test_tools_setup_is_correct(): void
    {
        $reflection = new ReflectionClass(SeoAgent::class);
        $method = $reflection->getMethod('tools');
        $method->setAccessible(true);

        $tools = $method->invoke($this->agent);

        $this->assertCount(1, $tools);
        $this->assertEquals('get_product_data', $tools[0]->getName());
    }

    public function test_tool_callable_executes_repository_method(): void
    {
        $productId = 123;
        $expectedData = ['title' => 'Test Produkt'];

        $this->repositoryMock->expects($this->once())
            ->method('getProductDataForSeo')
            ->with($productId)
            ->willReturn($expectedData);

        $agentReflection = new \ReflectionClass(SeoAgent::class);
        $toolsMethod = $agentReflection->getMethod('tools');
        $toolsMethod->setAccessible(true);

        $tools = $toolsMethod->invoke($this->agent);
        $productTool = $tools[0];

        $this->assertEquals('get_product_data', $productTool->getName());

        $toolReflection = new \ReflectionClass($productTool);
        $callback = null;

        foreach ($toolReflection->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($productTool);
            if (is_callable($value)) {
                $callback = $value;
                break;
            }
        }

        if ($callback) {
            $result = $callback($productId);
            $this->assertEquals($expectedData, $result);
        } else {
            $this->fail('Nie znaleziono wywoływalnej funkcji (callable) wewnątrz obiektu Tool.');
        }
    }
}
