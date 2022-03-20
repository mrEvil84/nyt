<?php

namespace App\Articles\ReadModel\Query;

class GetArticles
{
    private string $keyWord;

    public function __construct(string $keyWord = '')
    {
        $this->keyWord = $keyWord;
    }

    public function getKeyWord(): string
    {
        return $this->keyWord;
    }

    public function isKeywordSet(): bool
    {
        return $this->getKeyWord() !== '';
    }
}