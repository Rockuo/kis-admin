<?php

namespace App\Controller;

use App\Form\CashboxType;
use App\Form\LabelType;
use App\Service\ApiMiddleware;
use function Sodium\library_version_major;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LabelController extends AbstractController implements AuthenticatedController
{

    /** @var ApiMiddleware */
    private $apiMiddleware;

    /**
     * CashboxController constructor.
     * @param ApiMiddleware $apiMiddleware
     */
    public function __construct(ApiMiddleware $apiMiddleware)
    {
        $this->apiMiddleware = $apiMiddleware;
    }


    /**
     * @Route("/label", name="label")
     */
    public function index()
    {
        $labels = $this->apiMiddleware->getJSON(ApiMiddleware::ROUTE_LABELS);

        $forms = [];
        foreach ($labels as $label) {
            $forms[] = [
                'form' => $this->createForm(LabelType::class, $label)->createView(),
                'id' => $label['id']
            ];
        }
        $forms[] = [
            'form' => $this->createForm(LabelType::class, null, ['create' => true])->createView(),
            'id' => null
        ];

        return $this->render('label/index.html.twig', [
            'forms' => $forms,
        ]);
    }

    /**
     * @Route("/label/new", name="label_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function create(Request $request)
    {
        $form = $this->createForm(LabelType::class, null, ['create' => true]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->apiMiddleware->postJSON(ApiMiddleware::ROUTE_LABELS, [], [
                'name' => $data['name'],
                'color' => $data['color'],
            ]);
        }
        return $this->redirectToRoute('label');
    }


    /**
     * @Route("/label/{labelId}", name="label_edit")
     * @param Request $request
     * @param int $labelId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function edit(Request $request, int $labelId)
    {
        $labels = $this->apiMiddleware->getJSON(ApiMiddleware::ROUTE_LABELS, ['label_id' => $labelId]);
        $label = null;
        foreach ($labels as $sLabel) {
            if ($labelId === $sLabel['id']) {
                $label = $sLabel;
                break;
            }
        }

        $form = $this->createForm(LabelType::class, $label);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($label['name'] !== $data['name'] || $label['color'] !== $data['color']) {
                $this->apiMiddleware->putJSON(ApiMiddleware::ROUTE_LABELS_ID, ['label_id' => $labelId], [
                    'name' => $data['name'],
                    'color' => $data['color']
                ]);
            }
        }
        return $this->redirectToRoute('label');
    }

    /**
     * @Route("/label/{labelId}/delete", name="label_delete")
     * @param Request $request
     * @param int $labelId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, int $labelId)
    {
        $this->apiMiddleware->delete(ApiMiddleware::ROUTE_LABELS_ID, ['label_id' => $labelId]);

        return $this->redirectToRoute('label');
    }
}
