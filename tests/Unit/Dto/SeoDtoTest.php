<?php

namespace WooSimpleSeoAgent\Tests\Unit\Dto;

use PHPUnit\Framework\TestCase;
use WooSimpleSeoAgent\Dto\SeoDto;

class SeoDtoTest extends TestCase
{
    public function test_from_array_maps_all_fields_correctly(): void
    {
        $data = [
            'title' => 'Super Buty',
            'description' => 'Najlepsze buty we Wrocławiu.',
            'keywords' => 'buty, sport, wygoda',
            'shortDescription' => 'Krótki opis butów',
            'summary' => 'Poprawiono czytelność i dodano słowa kluczowe.'
        ];

        $dto = SeoDto::fromArray($data);

        $this->assertEquals($data['title'], $dto->title);
        $this->assertEquals($data['description'], $dto->description);
        $this->assertEquals($data['keywords'], $dto->keywords);
        $this->assertEquals($data['shortDescription'], $dto->shortDescription);
        $this->assertEquals($data['summary'], $dto->summary);
    }

    public function test_from_array_handles_missing_keys_with_defaults(): void
    {
        $data = [
            'title' => 'Tylko Tytuł',
            'description' => 'Tylko Opis'
        ];

        $dto = SeoDto::fromArray($data);

        $this->assertEquals('Tylko Tytuł', $dto->title);
        $this->assertEquals('', $dto->keywords);
        $this->assertEquals('', $dto->summary);
    }

    public function test_to_array_returns_all_properties(): void
    {
        $dto = new SeoDto(
            title:       'Test',
            description: 'Opis'
        );

        $array = $dto->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('title', $array);
        $this->assertArrayHasKey('summary', $array);
        $this->assertEquals('Test', $array['title']);
        $this->assertEquals('', $array['summary']);
    }
}