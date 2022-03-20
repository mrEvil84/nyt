<?php

declare(strict_types=1);

namespace App\Tests\Articles\Infrastructure;

use App\Articles\Infrastructure\NytMotoArticlesApiQueryUriFactory;
use App\Articles\ReadModel\Query\GetArticles;
use PHPUnit\Framework\TestCase;

class NytMotoArticlesApiQueryUriFactoryTest extends TestCase
{
    /**
     * @test
     * @dataProvider getQueryData
     */
    public function shouldGetMotoArticlesUriQuery(GetArticles $query, string $expectedUri): void
    {
        $sut = new NytMotoArticlesApiQueryUriFactory();

        $uri = $sut->getMotoArticlesUriQuery($query);

        $this->assertEquals($expectedUri, $uri);
    }

    public function getQueryData(): \Generator
    {
        yield [
            'query' => new GetArticles(),
            'expectedUri' => '?fq=news_desk:("Automobiles","Cars")&sort=newest&api-key=5hhZODMRrfUCQRqrRvqQQlZiiTcijncQ'
        ];

        yield [
            'query' => new GetArticles('mercedes-benz'),
            'expectedUri' => '?q=mercedes-benz&fq=news_desk:("Automobiles","Cars")&sort=newest&api-key=5hhZODMRrfUCQRqrRvqQQlZiiTcijncQ'
        ];
    }

}
