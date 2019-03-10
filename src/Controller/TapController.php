<?php

namespace App\Controller;

use App\Form\TapType;
use App\Service\ApiMiddleware;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TapController extends AbstractController implements AuthenticatedController
{

    /** @var ApiMiddleware */
    private $apiMiddleware;

    /**
     * tapController constructor.
     * @param ApiMiddleware $apiMiddleware
     */
    public function __construct(ApiMiddleware $apiMiddleware)
    {
        $this->apiMiddleware = $apiMiddleware;
    }


    /**
     * @Route("/tap", name="tap")
     */
    public function index()
    {
        $taps = $this->apiMiddleware->getJSON(ApiMiddleware::ROUTE_TAPS);

        $forms = [];
        foreach ($taps as $tap) {
            $forms[] = $this->createForm(TapType::class, $tap)->createView();
        }
        // nebudem jen tak přidávat pípy
//        $forms[] = $this->createForm(TapType::class, null, ['create' => true])->createView();

        return $this->render('tap/index.html.twig', [
            'forms' => $forms,
        ]);
    }

    /**
     * @Route("/tap/new", name="tap_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function create(Request $request)
    {
        $form = $this->createForm(TapType::class, null, ['create' => true]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->apiMiddleware->postJSON(ApiMiddleware::ROUTE_TAPS, [],['name' => $form->getData()['name']]);
        }
        return $this->redirectToRoute('tap');
    }


    /**
     * @Route("/tap/{tapId}", name="tap_edit")
     * @param Request $request
     * @param int $tapId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function edit(Request $request, int $tapId)
    {
        $tap = $this->apiMiddleware->getJSON(ApiMiddleware::ROUTE_TAPS_ID, ['tap_id' => $tapId]);
        $form = $this->createForm(TapType::class, $tap);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if($tap['name'] !== $data['name']) {
                $this->apiMiddleware->putJSON(ApiMiddleware::ROUTE_TAPS_ID, ['tap_id' => $tapId],['name' => $data['name']]);
            }
        }
        return $this->redirectToRoute('tap');
    }

}
