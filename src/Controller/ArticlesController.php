<?php

namespace App\Controller;

use App\Form\ArticleType;
use App\Service\ApiMiddleware;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticlesController extends AbstractController implements AuthenticatedController
{

    /**
     * @var ApiMiddleware
     */
    protected $apiMiddleware;

    /**
     * ArticlesController constructor.
     * @param ApiMiddleware $apiMiddleware
     */
    public function __construct(ApiMiddleware $apiMiddleware)
    {
        $this->apiMiddleware = $apiMiddleware;
    }


    /**
     * @Route("/articles", name="articles")
     */
    public function index()
    {
        $articles = $this->apiMiddleware->getJSON(ApiMiddleware::ROUTE_ARTICLES);

        return $this->render('articles/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/articles/{articleId}", name="articles_edit")
     * @param Request $request
     * @param int $articleId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editArticle(Request $request, int $articleId)
    {
        $userData = $this->apiMiddleware->getJSON(ApiMiddleware::ROUTE_ARTICLES_ID, ['article_id' => $articleId]);
        $labels = $this->apiMiddleware->getJSON(ApiMiddleware::ROUTE_LABELS);

        $articles = $this->apiMiddleware->getJSON(ApiMiddleware::ROUTE_ARTICLES);
        $allArticles = [];
        foreach ($articles as $article) {

            if(array_key_exists($article['name'], $allArticles))
            {
                $article['name'] = $article['name'].'('.$article['id'].')';
            }
            $allArticles[$article['name']] = $article['id'];
        }

        $formLabels = [];
        foreach ($labels as $label)
        {
            $formLabels[$label['name']] = $label['id'];
        }

        $form = $this->createForm(ArticleType::class, $userData, ['labelsAll' => $formLabels,'allArticles' => $allArticles]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            foreach ($formData as $key => $value) {
                if ($key === 'image' && $value instanceof UploadedFile) {
                    $this->apiMiddleware->sendFile(ApiMiddleware::ROUTE_ARTICLES_IMAGE, $value, ['article_id' => $articleId]);
                    } elseif ($key === 'labels') {
                        if ($userData[$key] != $value) // != JE SPRÁVNĚ, nezáleží nám na pořadí
                        {
                            $this->apiMiddleware->putJSON(
                                ApiMiddleware::ROUTE_ARTICLES_LABELS,
                                ['article_id' => $articleId],
                                $value
                            );
                        }
                    } elseif ($key === 'components') {
                        if ($userData[$key] != $value) // != JE SPRÁVNĚ, nezáleží nám na pořadí
                        {
                            $components = [];
                            foreach ($value as $component)
                            {
                                if($component['id']) {
                                    $components[] = ['amount' => $component['amount'], 'component' => $component['id']];
                                }
                            }
                            $this->apiMiddleware->putJSON(
                                ApiMiddleware::ROUTE_ARTICLES_COMPONENTS,
                                ['article_id' => $articleId],
                                $components
                            );
                        }
                    } elseif ($key === 'tariffs') {
                        if ($userData[$key] != $value) // != JE SPRÁVNĚ, nezáleží nám na pořadí
                        {
                            $this->apiMiddleware->putJSON(
                                ApiMiddleware::ROUTE_ARTICLES_TARIFFS,
                                ['article_id' => $articleId],
                                array_values($value)
                            );
                        }
                    }
//                elseif ($value !== $userData[$key]) {
//                    $this->apiMiddleware->putJSON(self::ROUTE_MAP[$key], ['user_id' => $userId], [$key => $value?:null]);
//                }
            }
        }
        return $this->render('articles/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
