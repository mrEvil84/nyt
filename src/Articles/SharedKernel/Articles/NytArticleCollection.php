<?php

namespace App\Articles\SharedKernel\Articles;

class NytArticleCollection
{
    /**
     * @var Article[]
     */
    private array $articles;

    // a factory method
    public function fromRawData(array $rawDocsData, Article $articleType): self
    {
        $arts = [];
        foreach ($rawDocsData as $rawData) {
            $arts[] = $articleType->fromRawData($rawData);
        }

        $instance = new self();
        $instance->setArticles($arts);
        return $instance;
    }

    public function toArray(): array
    {
        $collection = [];
        foreach ($this->articles as $article) {
            $collection[] = $article->toArray();
        }
        return $collection;
    }

    /**
     * @param Article[] $articles
     */
    private function setArticles(array $articles): void
    {
        $this->articles = $articles;
    }
}