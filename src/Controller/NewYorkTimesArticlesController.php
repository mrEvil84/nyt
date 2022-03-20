<?php

declare(strict_types=1);

namespace App\Controller;

use App\Articles\Exceptions\ApiResponseError;
use App\Articles\ReadModel\NytArticlesReadModel;
use App\Articles\ReadModel\Query\GetArticles as GetArticlesQuery;
use Exception;
use HtmlSanitizer\SanitizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewYorkTimesArticlesController extends AbstractController
{

    private NytArticlesReadModel $newYorkTimesArticlesReadModel;
    private SanitizerInterface $sanitizer;

    public function __construct(
        NytArticlesReadModel $newYorkTimesArticlesReadModel,
        SanitizerInterface   $sanitizer
    ) {
        $this->newYorkTimesArticlesReadModel = $newYorkTimesArticlesReadModel;
        $this->sanitizer = $sanitizer;
    }

    /**
     * @Route("/nytimes/{queryToFindInBody}", name="get_nytimes_artices", methods={"GET"})
     */
    public function getArticles(string $queryToFindInBody = ''): JsonResponse
    {
        try {
            $articlesCollection = $this
                ->newYorkTimesArticlesReadModel
                ->getArticles(
                    new GetArticlesQuery(
                        $this->sanitizer->sanitize($queryToFindInBody)
                    )
                );

            return new JsonResponse($articlesCollection->toArray());
        } catch (ApiResponseError | Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}