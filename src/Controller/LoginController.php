<?php

namespace App\Controller;

use App\Service\ApiMiddleware;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController implements AuthenticatedController
{

    /**
     * @var ApiMiddleware
     */
    private $apiMiddleware;

    /**
     * LoginController constructor.
     * @param ApiMiddleware $apiMiddleware
     */
    public function __construct(ApiMiddleware $apiMiddleware)
    {
        $this->apiMiddleware = $apiMiddleware;
    }

    /**
     * @Route("/login", name="login")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        if ($eduIdSession = $request->get('session_id')) {
            $request->getSession()->set('eduId_session', $eduIdSession);
            $apiLoginResponse =  $this->apiMiddleware->get(ApiMiddleware::ROUTE_AUTH_EDUID_LOGIN, ['session_id' => $eduIdSession]);
            $apiLoginResponseData = json_decode($apiLoginResponse->getBody()->getContents(), true);


            $request->getSession()->set('auth_data', $apiLoginResponseData);
            $this->apiMiddleware->initClient($request->getSession());


            $meLoginResponse =  $this->apiMiddleware->get(ApiMiddleware::ROUTE_USERS_ME);
            $meLoginResponseData = json_decode($meLoginResponse->getBody()->getContents(), true);

            $request->getSession()->set('user_data', [
                'email' => $meLoginResponseData['email'],
                'nickname' => $meLoginResponseData['nickname'],
                'role' => $meLoginResponseData['role'],
            ]);

            if($origin = $request->getSession()->get('origin'))
            {
                $request->getSession()->set('origin', null);
                return $this->redirect($origin);
            }

            return $this->render('login/index.html.twig', [
                'controller_name' => 'LoginController',
            ]);
        } else {
            $response = $this->apiMiddleware->get(ApiMiddleware::ROUTE_AUTH_EDUID, ['redirect' => 'http://localhost:8000/login']);
            $responseData = json_decode($response->getBody()->getContents(), true);
            return $this->redirect($responseData['wayf_url']);
        }

    }

    /**
     * @Route("/not-login", name="not_login")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function notLogin(Request $request)
    {

        $form = $this->createFormBuilder()
            ->add('image', FileType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Task'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form['image']->getData();
//            $this->sendFile($file, $request->getSession());
//            $contents = $file->fread($file->getSize());

        }
        return $this->render('login/notLogin.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}
