<?php

namespace App\Articles\Infrastructure;

use App\Articles\ReadModel\Query\GetArticles;

class NytMotoArticlesApiQueryUriFactory
{
    private const NYT_API_KEY = '5hhZODMRrfUCQRqrRvqQQlZiiTcijncQ';

    public function getMotoArticlesUriQuery(GetArticles $query): string
    {
        if ($query->isKeywordSet()) {
            $queryUri = $this->appendApiKey('?q=' . $query->getKeyWord() . '&fq=news_desk:("Automobiles","Cars")&sort=newest');
        } else {
            $queryUri = $this->appendApiKey('?fq=news_desk:("Automobiles","Cars")&sort=newest');
        }

        return $queryUri;
    }

    public function appendApiKey(string $query): string
    {
        return $query . '&api-key=' . self::NYT_API_KEY;
    }
}