<?php

declare(strict_types=1);

namespace App\Articles\SharedKernel\Articles;

class NytMotoArticleExtended implements Article
{
    // a decorator

    private Article $article;
    private string $section;
    private string $subsection;

    private function setData(Article $article, string $section, string $subsection): void
    {
        $this->article = $article;
        $this->section = $section;
        $this->subsection = $subsection;
    }

    public function fromRawData(array $rawData): self
    {
        $nytArticle = new NytMotoArticle();
        $instance = new self();

        $instance->setData(
            $nytArticle->fromRawData($rawData),
            array_key_exists('section_name', $rawData) ? $rawData['section_name'] : '-empty-',
            array_key_exists('subsection_name', $rawData) ? $rawData['subsection_name'] : '-empty-'
        );

        return $instance;
    }

    public function toArray(): array
    {
        return array_merge(
            $this->article->toArray(),
            ['section' => $this->section, 'subsection' => $this->subsection]
        );
    }
}