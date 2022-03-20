<?php

declare(strict_types=1);

namespace App\Articles\ReadModel;

use App\Articles\Exceptions\ApiResponseError;
use App\Articles\ReadModel\Query\GetArticles;
use App\Articles\SharedKernel\Articles\NytArticleCollection;

interface NytArticlesRepository
{
    /**
     * @throws ApiResponseError
     */
    public function getArticles(GetArticles $query): NytArticleCollection;
}