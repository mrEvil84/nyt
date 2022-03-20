<?php

namespace App\Articles\ReadModel;

use App\Articles\Exceptions\ApiResponseError;
use App\Articles\ReadModel\Query\GetArticles;
use App\Articles\SharedKernel\Articles\NytArticleCollection;

class NytArticlesReadModel
{
    private NytArticlesRepository $newYorkTimesArticleRepository;

    /**
     * @param NytArticlesRepository $newYorkTimesArticleRepository
     */
    public function __construct(NytArticlesRepository $newYorkTimesArticleRepository)
    {
        $this->newYorkTimesArticleRepository = $newYorkTimesArticleRepository;
    }

    /**
     * @throws ApiResponseError
     */
    public function getArticles(GetArticles $query): NytArticleCollection
    {
        return $this->newYorkTimesArticleRepository->getArticles($query);
    }
}