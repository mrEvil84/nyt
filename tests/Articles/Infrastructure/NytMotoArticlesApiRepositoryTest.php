<?php

namespace App\Tests\Articles\Infrastructure;

use App\Articles\Exceptions\ApiResponseError;
use App\Articles\Infrastructure\NytMotoArticlesApiQueryUriFactory;
use App\Articles\Infrastructure\NytMotoArticlesApiRepository;
use App\Articles\ReadModel\Query\GetArticles;
use App\Articles\SharedKernel\Articles\NytArticleCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class NytMotoArticlesApiRepositoryTest extends TestCase
{
    /**
     * @test
     * @dataProvider getTestCasesData
     */
    public function shouldGetArticles(GetArticles $query, array $rawResponse): void
    {
        $uriFactory = new NytMotoArticlesApiQueryUriFactory();
        $uri = $uriFactory->getMotoArticlesUriQuery($query);

        $response = $this->createMock(ResponseInterface::class);
        $response->expects(self::once())->method('getStatusCode')->willReturn(Response::HTTP_OK);
        $response->expects(self::once())->method('toArray')->willReturn($rawResponse);

        $nytArticlesClient = $this->createMock(HttpClientInterface::class);
        $nytArticlesClient
            ->expects(self::once())
            ->method('request')
            ->with('GET', $uri)
            ->willReturn($response);

        $sut = new NytMotoArticlesApiRepository(
            $nytArticlesClient,
            new NytMotoArticlesApiQueryUriFactory(),
            new NytArticleCollection()
        );

        $sut->getArticles($query);
    }

    /**
     * @test
     */
    public function shouldNotGetArticlesWhenWrongResponseStatusCode(): void
    {
        $uriFactory = new NytMotoArticlesApiQueryUriFactory();
        $uri = $uriFactory->getMotoArticlesUriQuery(new GetArticles());

        $response = $this->createMock(ResponseInterface::class);
        $response->expects(self::once())->method('getStatusCode')->willReturn(Response::HTTP_GATEWAY_TIMEOUT);

        $nytArticlesClient = $this->createMock(HttpClientInterface::class);
        $nytArticlesClient
            ->expects(self::once())
            ->method('request')
            ->with('GET', $uri)
            ->willReturn($response);


        $sut = new NytMotoArticlesApiRepository(
            $nytArticlesClient,
            new NytMotoArticlesApiQueryUriFactory(),
            new NytArticleCollection()
        );

        $this->expectException(ApiResponseError::class);
        $this->expectErrorMessage('NewYorkTimes api error. Try later.');
        $sut->getArticles(new GetArticles());
    }

    /**
     * @test
     * @dataProvider getDataWithBadDataResponses
     */
    public function shouldNotGetArticlesWhenWrongDataStatus(array $rawResponse, string $expectedExceptionMessage): void
    {
        $uriFactory = new NytMotoArticlesApiQueryUriFactory();
        $uri = $uriFactory->getMotoArticlesUriQuery(new GetArticles());

        $response = $this->createMock(ResponseInterface::class);
        $response->expects(self::once())->method('getStatusCode')->willReturn(Response::HTTP_OK);
        $response->expects(self::once())->method('toArray')->willReturn($rawResponse);

        $nytArticlesClient = $this->createMock(HttpClientInterface::class);
        $nytArticlesClient
            ->expects(self::once())
            ->method('request')
            ->with('GET', $uri)
            ->willReturn($response);


        $sut = new NytMotoArticlesApiRepository(
            $nytArticlesClient,
            new NytMotoArticlesApiQueryUriFactory(),
            new NytArticleCollection()
        );

        $this->expectException(ApiResponseError::class);
        $this->expectErrorMessage($expectedExceptionMessage);
        $sut->getArticles(new GetArticles());
    }

    public function getTestCasesData(): \Generator
    {
        yield [
            'query' => new GetArticles(),
            'rawResponseData' => [
                'status' => 'OK',
                'response' => [
                    'docs' => [
                        [
                            'headline' => ['main' => 'abcd'],
                            'pub_date' => '2018-06-01T20:06:14+0000',
                            'lead_paragraph' => 'foo bar baz bar',
                            'multimedia' => [
                                [
                                    'subtype' => 'superJumbo',
                                    'url' => '/jumboImageUrl'
                                ],
                            ],
                            'web_url' => 'http://article.url',
                        ],
                    ]
                ],
            ],
        ];
        yield [
            'query' => new GetArticles('foo-bar'),
            'rawResponseData' => [
                'status' => 'OK',
                'response' => [
                    'docs' => [
                        [
                            'headline' => ['main' => 'abcd'],
                            'pub_date' => '2018-06-01T20:06:14+0000',
                            'lead_paragraph' => 'foo bar baz bar',
                            'multimedia' => [
                                [
                                    'subtype' => 'superJumbo',
                                    'url' => '/jumboImageUrl'
                                ],
                            ],
                            'web_url' => 'http://article.url',
                            'section_name' => 'Automotive',
                            'subsection_name' => 'Cars'
                        ],
                    ]
                ],
            ],
        ];
    }

    public function getDataWithBadDataResponses(): \Generator
    {
        yield [
            'rawResponse' => [
                'status' => 'Failed',
            ],
            'expectedExceptionMessage' => 'NewYorkTimes api error: Wrong data status.'
        ];
        yield [
            'rawResponse' => [
                'status' => 'OK',
                'response' => [],
            ],
            'expectedExceptionMessage' => 'NewYorkTimes api error: Empty response.'
        ];
    }
}
