<?php

declare(strict_types=1);


namespace WooSimpleSeoAgent\Dto;


use NeuronAI\StructuredOutput\SchemaProperty;

final readonly class SeoDto
{
    public function __construct(
        #[SchemaProperty(description: 'Title of the product.')]
        public string $title = '',

        #[SchemaProperty(description: 'Description of the product.', required: true)]
        public string $description = '',

        #[SchemaProperty(description: 'Keywords of the product page.')]
        public string $keywords = '',

        #[SchemaProperty(description: 'Short description of the product.')]
        public string $shortDescription = '',

        #[SchemaProperty(description: 'Summary of evaluation and changes that was made. Additional information for the user.')]
        public string $summary = ''
    ) {
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title:            $data['title'] ?? '',
            description:      $data['description'] ?? '',
            keywords:         $data['keywords'] ?? '',
            shortDescription: $data['shortDescription'] ?? '',
            summary:          $data['summary'] ?? ''
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}