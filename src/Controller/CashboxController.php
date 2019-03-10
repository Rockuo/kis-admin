<?php

namespace App\Controller;

use App\Form\CashboxType;
use App\Service\ApiMiddleware;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CashboxController extends AbstractController implements AuthenticatedController
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
     * @Route("/cashbox", name="cashbox")
     */
    public function index()
    {
        $cashboxes = $this->apiMiddleware->getJSON(ApiMiddleware::ROUTE_CASHBOXES);

        $forms = [];
        foreach ($cashboxes as $cashbox) {
            $cashboxData = $this->apiMiddleware->getJSON(ApiMiddleware::ROUTE_CASHBOX_ID, ['cashbox_id' => $cashbox['id']]);
            $forms[] = $this->createForm(CashboxType::class, $cashboxData)->createView();
        }
        $forms[] = $this->createForm(CashboxType::class, null, ['create' => true])->createView();

        return $this->render('cashbox/index.html.twig', [
            'forms' => $forms,
        ]);
    }

    /**
     * @Route("/cashbox/new", name="cashbox_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function create(Request $request)
    {
        $form = $this->createForm(CashboxType::class, null, ['create' => true]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->apiMiddleware->postJSON(ApiMiddleware::ROUTE_CASHBOXES, [],['name' => $form->getData()['name']]);
        }
        return $this->redirectToRoute('cashbox');
    }


    /**
     * @Route("/cashbox/{cashboxId}", name="cashbox_edit")
     * @param Request $request
     * @param int $cashboxId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function edit(Request $request, int $cashboxId)
    {
        $cashbox = $this->apiMiddleware->getJSON(ApiMiddleware::ROUTE_CASHBOX_ID, ['cashbox_id' => $cashboxId]);
        $form = $this->createForm(CashboxType::class, $cashbox);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if($cashbox['name'] !== $data['name']) {
                $this->apiMiddleware->putJSON(ApiMiddleware::ROUTE_CASHBOX_ID, ['cashbox_id' => $cashboxId],['name' => $data['name']]);
            }
        }
        return $this->redirectToRoute('cashbox');
    }

}
