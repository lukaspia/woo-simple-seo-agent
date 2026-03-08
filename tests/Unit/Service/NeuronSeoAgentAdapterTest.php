<?php

declare(strict_types=1);

namespace WooSimpleSeoAgent\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use NeuronAI\AgentInterface;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Chat\Messages\Message;
use NeuronAI\StructuredOutput\JsonExtractor;
use WooSimpleSeoAgent\Dto\SeoDto;
use WooSimpleSeoAgent\Service\NeuronSeoAgentAdapter;

class NeuronSeoAgentAdapterTest extends TestCase
{
    private $agentMock;
    private $extractorMock;
    private $adapter;

    protected function setUp(): void
    {
        $this->agentMock = $this->createMock(AgentInterface::class);
        $this->extractorMock = $this->createMock(JsonExtractor::class);
        $this->adapter = new NeuronSeoAgentAdapter($this->agentMock, $this->extractorMock);
    }

    public function test_generate_seo_content_returns_dto_on_success(): void
    {
        $prompt = "Test prompt";
        $jsonOnly = '{"title": "SEO Title", "description": "SEO Desc"}';

        $messageMock = $this->createMock(Message::class);
        $messageMock->method('getContent')->willReturn("Raw AI output with json");

        $this->agentMock->expects($this->once())
            ->method('chat')
            ->willReturn($messageMock);

        $this->extractorMock->expects($this->once())
            ->method('getJson')
            ->willReturn($jsonOnly);

        $result = $this->adapter->generateSeoContent($prompt);

        $this->assertInstanceOf(SeoDto::class, $result);
        $this->assertEquals('SEO Title', $result->title);
    }

    public function test_enrich_prompt_with_context_merges_history(): void
    {
        $context = ['conversationHistory' => ['User: Hello']];
        $messageMock = $this->createMock(Message::class);
        $messageMock->method('getContent')->willReturn('{"title": "ok"}');

        $this->agentMock->expects($this->once())
            ->method('chat')
            ->with($this->callback(function ($msg) {
                $content = is_array($msg) ? '' : $msg->getContent();
                return str_contains($content, '### Previous Conversation Context:');
            }))
            ->willReturn($messageMock);

        $this->extractorMock->method('getJson')->willReturn('{"title": "ok"}');

        $this->adapter->generateSeoContent("Hi", $context);
    }
}