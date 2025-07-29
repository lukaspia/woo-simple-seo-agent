<?php

declare(strict_types=1);

namespace WooSimpleSeoAgent\Tests\Unit\Service;

use NeuronAI\Chat\Messages\Message;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\StructuredOutput\JsonExtractor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WooSimpleSeoAgent\Neuron\SeoAgent as NeuronSeoAgent;
use WooSimpleSeoAgent\Service\NeuronSeoAgentAdapter;

class NeuronSeoAgentAdapterTest extends TestCase
{
    private MockObject|NeuronSeoAgent $neuronSeoAgentMock;
    private MockObject|JsonExtractor $jsonExtractorMock;
    private NeuronSeoAgentAdapter $adapter;

    protected function setUp(): void
    {
        $this->neuronSeoAgentMock = $this->createMock(NeuronSeoAgent::class);
        $this->jsonExtractorMock = $this->createMock(JsonExtractor::class);
        $this->adapter = new NeuronSeoAgentAdapter(
            $this->neuronSeoAgentMock,
            $this->jsonExtractorMock
        );
    }

    public function testGenerateSeoContentWithEmptyContext(): void
    {
        $prompt = 'Test prompt';
        $expectedResponse = ['title' => 'Test Title', 'description' => 'Test Description'];

        $message = $this->createMock(Message::class);
        $message->method('getContent')
            ->willReturn('dummy');

        $this->neuronSeoAgentMock->expects($this->once())
            ->method('chat')
            ->with($this->callback(function ($arg) use ($prompt) {
                return $arg instanceof UserMessage && $arg->getContent() === $prompt;
            }))
            ->willReturn($message);

        $this->jsonExtractorMock->expects($this->once())
            ->method('getJson')
            ->with('dummy')
            ->willReturn(json_encode($expectedResponse));

        $result = $this->adapter->generateSeoContent($prompt);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testGenerateSeoContentWithConversationHistory(): void
    {
        $prompt = 'Test prompt';
        $history = ['Message 1', 'Message 2'];
        $context = ['conversationHistory' => $history];
        $expectedResponse = ['title' => 'Test Title', 'description' => 'Test Description'];

        $message = $this->createMock(Message::class);
        $message->method('getContent')
            ->willReturn('dummy');

        $this->neuronSeoAgentMock->expects($this->once())
            ->method('chat')
            ->with($this->callback(function ($arg) use ($prompt, $history) {
                $expectedContent = $prompt . '. Here is our conversation history: ' . implode(',', $history);
                return $arg->getContent() === $expectedContent;
            }))
            ->willReturn($message);

        $this->jsonExtractorMock->expects($this->once())
            ->method('getJson')
            ->willReturn(json_encode($expectedResponse));

        $result = $this->adapter->generateSeoContent($prompt, $context);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testGenerateSeoContentWithJsonError(): void
    {
        $message = $this->createMock(Message::class);
        $message->method('getContent')
            ->willReturn('dummy');

        $this->neuronSeoAgentMock->method('chat')
            ->willReturn($message);

        $this->jsonExtractorMock->method('getJson')
            ->willReturn('invalid json');

        $this->expectException(\JsonException::class);
        $this->adapter->generateSeoContent('test');
    }
}
