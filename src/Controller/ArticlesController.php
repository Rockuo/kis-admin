<?php

namespace App\Controller;

use App\Form\ArticleBasicsType;
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


    private function createArticleForm($data = null)
    {
        $labels = $this->apiMiddleware->getJSON(ApiMiddleware::ROUTE_LABELS);

        $articles = $this->apiMiddleware->getJSON(ApiMiddleware::ROUTE_ARTICLES);
        $allArticles = [];
        foreach ($articles as $article) {

            if (array_key_exists($article['name'], $allArticles)) {
                $article['name'] = $article['name'] . '(' . $article['id'] . ')';
            }
            $allArticles[$article['name']] = $article['id'];
        }

        $formLabels = [];
        foreach ($labels as $label) {
            $formLabels[$label['name']] = $label['id'];
        }


        $emptyKegs = null;
        $inheritableKegsChoices = null;
        if ($data['beer_keg']) {
            $inheritableKegs = $this->apiMiddleware->getJSON(ApiMiddleware::ROUTE_INHERITABLE_KEGS);
            $inheritableKegsChoices = [];
            foreach ($inheritableKegs as $inheritableKeg) {
                $inheritableKegsChoices[$inheritableKeg['name']] = $inheritableKeg['id'];
            }

            $emptyKegs = [];
        }



        return $this->createForm(ArticleType::class, $data, [
            'labelsAll' => $formLabels,
            'allArticles' => $allArticles,
            'empty_kegs' => $emptyKegs,
            'inheritable_kegs' => $inheritableKegsChoices,
        ]);

    }

    /**
     * @Route("/articles/new", name="articles_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newArticle(Request $request)
    {
        $form = $this->createForm(ArticleBasicsType::class, null, ['submit' => true]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            if ($formData['keg']) {
                $formData['beer_keg'] = [
                    'empty_keg' => null,
                    'inherit_products' => null,
                    'volume' => 1
                ];
            } else {
                $formData['beer_keg'] = null;
            }
            unset($formData['keg']);
            $result = $this->apiMiddleware->postJSON(
                ApiMiddleware::ROUTE_ARTICLES,
                [],
                $formData
            );
            return $this->redirectToRoute('articles_edit', ['articleId' => $result['id']]);

        }
        return $this->render('articles/edit.html.twig', [
            'form' => $form->createView(),
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
        $form = $this->createArticleForm($userData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            foreach ($formData as $key => $value) {
                if ($key === 'image' && $value instanceof UploadedFile) {
                    $this->apiMiddleware->sendFile(ApiMiddleware::ROUTE_ARTICLES_IMAGE, $value, ['article_id' => $articleId]);
                }
                if ($key === 'labels') {
                    if ($userData[$key] != $value) // != JE SPRÁVNĚ, nezáleží nám na pořadí
                    {
                        $this->apiMiddleware->putJSON(
                            ApiMiddleware::ROUTE_ARTICLES_LABELS,
                            ['article_id' => $articleId],
                            $value
                        );
                    }
                }
                if ($key === 'components') {
                    if ($userData[$key] != $value) // != JE SPRÁVNĚ, nezáleží nám na pořadí
                    {
                        $components = [];
                        foreach ($value as $component) {
                            if ($component['id']) {
                                $components[] = ['amount' => $component['amount'], 'component' => $component['id']];
                            }
                        }
                        $this->apiMiddleware->putJSON(
                            ApiMiddleware::ROUTE_ARTICLES_COMPONENTS,
                            ['article_id' => $articleId],
                            $components
                        );
                    }
                }
                if ($key === 'tariffs') {
                    if ($userData[$key] != $value) // != JE SPRÁVNĚ, nezáleží nám na pořadí
                    {
                        $this->apiMiddleware->putJSON(
                            ApiMiddleware::ROUTE_ARTICLES_TARIFFS,
                            ['article_id' => $articleId],
                            array_values($value)
                        );
                    }
                }
                if ($key === 'basics') {
                    if (
                        $value['name'] !== $userData['name'] ||
                        $value['unit'] !== $userData['unit'] ||
                        (
                            isset($value['beer_keg']) && $value['beer_keg'] != $userData['beer_keg']
                        )
                    ) // != JE SPRÁVNĚ, nezáleží nám na pořadí
                    {
                        if (!isset($value['beer_keg'])) {
                            $value['beer_keg'] = null;
                        } else {
                            $value['beer_keg']['empty_keg'] = $value['beer_keg']['empty_keg'] ?
                                (int)$value['beer_keg']['empty_keg'] :
                                null;

                            $value['beer_keg']['inherit_products'] = $value['beer_keg']['inherit_products'] ?
                                (int)$value['beer_keg']['inherit_products'] :
                                null;
                        }
                        $this->apiMiddleware->putJSON(
                            ApiMiddleware::ROUTE_ARTICLES_ID,
                            ['article_id' => $articleId],
                            $value
                        );
                    }
                }
            }
        }
        return $this->render('articles/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
