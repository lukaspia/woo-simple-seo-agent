<?php

declare(strict_types=1);


namespace WooSimpleSeoAgent\Dto;


use NeuronAI\StructuredOutput\SchemaProperty;

class Seo
{
    #[SchemaProperty(description: 'Title of the product.')]
    private string $title = '';
    #[SchemaProperty(description: 'Description of the product.', required: true)]
    private string $description = '';
    #[SchemaProperty(description: 'Keywords of the product page.')]
    private string $keywords = '';
    #[SchemaProperty(description: 'Short description of the product.')]
    private string $shortDescription = '';
    #[SchemaProperty(description: 'Summary of evaluation and changes that was made. Additional information for the user.')]
    private string $summary = '';

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getKeywords(): string
    {
        return $this->keywords;
    }

    public function setKeywords(string $keywords): void
    {
        $this->keywords = $keywords;
    }

    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(string $shortDescription): void
    {
        $this->shortDescription = $shortDescription;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): void
    {
        $this->summary = $summary;
    }
}