<?php

namespace App\Articles\Infrastructure;

use App\Articles\Exceptions\ApiResponseError;
use App\Articles\ReadModel\NytArticlesRepository;
use App\Articles\ReadModel\Query\GetArticles;
use App\Articles\SharedKernel\Articles\Article;
use App\Articles\SharedKernel\Articles\NytArticleCollection;
use App\Articles\SharedKernel\Articles\NytMotoArticle;
use App\Articles\SharedKernel\Articles\NytMotoArticleExtended;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NytMotoArticlesApiRepository implements NytArticlesRepository
{
    private HttpClientInterface $httpClient;
    private NytMotoArticlesApiQueryUriFactory $queryUriFactory;
    private NytArticleCollection $articleCollection;

    public function __construct(
        HttpClientInterface $nytSearchArticlesClient,
        NytMotoArticlesApiQueryUriFactory $queryUriFactory,
        NytArticleCollection $articleCollection
    ) {
        $this->httpClient = $nytSearchArticlesClient;
        $this->queryUriFactory = $queryUriFactory;
        $this->articleCollection = $articleCollection;
    }

    /**
     * @throws ApiResponseError
     * @throws TransportExceptionInterface
     */
    public function getArticles(GetArticles $query): NytArticleCollection
    {
        $queryUri = $this->queryUriFactory->getMotoArticlesUriQuery($query);

        $response = $this->httpClient->request('GET', $queryUri);


        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw ApiResponseError::apiResponseError('NewYorkTimes api error. Try later.');
        }

        $responseRawData = $response->toArray();

        $this->checkResponseData($responseRawData);

        return $this->articleCollection->fromRawData(
            $responseRawData['response']['docs'],
            $this->getArticleType($query)
        );
    }

    private function getArticleType(GetArticles $query): Article
    {
        if ($query->isKeywordSet()) {
            return new NytMotoArticleExtended();
        }

        return new NytMotoArticle();
    }

    private function checkResponseData(array $responseRawData): void
    {
        if ($responseRawData['status'] !== 'OK') {
            throw ApiResponseError::apiResponseError('NewYorkTimes api error: Wrong data status.');
        }

        if (empty($responseRawData['response'])) {
            throw ApiResponseError::apiResponseError('NewYorkTimes api error: Empty response.');
        }
    }
}