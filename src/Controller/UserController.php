<?php

namespace App\Controller;

use App\Form\UserType;
use App\Service\ApiMiddleware;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController implements AuthenticatedController
{

    const ROUTE_MAP = [
        'email' => ApiMiddleware::ROUTE_USERS_EMAIL,
        'gamification_consent' => ApiMiddleware::ROUTE_USERS_GAM,
        'name' => ApiMiddleware::ROUTE_USERS_NAME,
        'nickname' => ApiMiddleware::ROUTE_USERS_NICKNAME,
        'role' => ApiMiddleware::ROUTE_USERS_ROLE,
    ];


    /**
     * @var ApiMiddleware
     */
    protected $apiMiddleware;

    /**
     * UserController constructor.
     * @param ApiMiddleware $apiMiddleware
     */
    public function __construct(ApiMiddleware $apiMiddleware)
    {
        $this->apiMiddleware = $apiMiddleware;
    }


    /**
     * @Route("/users", name="users")
     */
    public function index()
    {
        $users = $this->apiMiddleware->getJSON(ApiMiddleware::ROUTE_USERS);

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/users/{userId}", name="users_edit")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editUser(Request $request, int $userId)
    {
        $userData = $this->apiMiddleware->getJSON(ApiMiddleware::ROUTE_USERS_ID, ['user_id' => $userId]);

        $form = $this->createForm(UserType::class, $userData, ['me' => $userId === $request->getSession()->get('user_data')['id']]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            foreach ($formData as $key => $value) {
                if($key === 'pin') {
                    if($value) {
                        $this->apiMiddleware->putJSON(ApiMiddleware::ROUTE_ME_PIN, [], ['pin' => $value]);
                    }
                } elseif ($value !== $userData[$key]) {
                    $this->apiMiddleware->putJSON(self::ROUTE_MAP[$key], ['user_id' => $userId], [$key => $value?:null]);
                }
            }
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
