<?php

namespace App\Controller;

use App\Service\ApiMiddleware;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @throws \Exception
     */
    public function login(Request $request)
    {
        if($this->authDataReady($request->getSession()))
        {
            return $this->redirectToRoute('home');
        }


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

            return $this->redirectToRoute('home');
        } else {
            $response = $this->apiMiddleware->get(ApiMiddleware::ROUTE_AUTH_EDUID, ['redirect' => $_SERVER['kis-prefix'].$this->generateUrl('login')]);
            $responseData = json_decode($response->getBody()->getContents(), true);
            return $this->redirect($responseData['wayf_url']);
        }

    }

//    /**
//     * @Route("/register", name="register")
//     * @param Request $request
//     * @return \Symfony\Component\HttpFoundation\Response
//     * @throws \Exception
//     */
//    public function register(Request $request)
//    {
//        if($this->authDataReady($request->getSession()))
//        {
//            return $this->redirectToRoute('home');
//        }
//
//        if ($eduIdSession = $request->get('session_id')) {
//            $request->getSession()->set('eduId_session', $eduIdSession);
//            $apiLoginResponse =  $this->apiMiddleware->get(ApiMiddleware::ROUTE_AUTH_EDUID_LOGIN, ['session_id' => $eduIdSession]);
//            $apiLoginResponseData = json_decode($apiLoginResponse->getBody()->getContents(), true);
//
//
//            $request->getSession()->set('auth_data', $apiLoginResponseData);
//            $this->apiMiddleware->initClient($request->getSession());
//
//
//            $meLoginResponse =  $this->apiMiddleware->get(ApiMiddleware::ROUTE_USERS_ME);
//            $meLoginResponseData = json_decode($meLoginResponse->getBody()->getContents(), true);
//
//            $request->getSession()->set('user_data', [
//                'email' => $meLoginResponseData['email'],
//                'nickname' => $meLoginResponseData['nickname'],
//                'role' => $meLoginResponseData['role'],
//            ]);
//
//            if($origin = $request->getSession()->get('origin'))
//            {
//                $request->getSession()->set('origin', null);
//                return $this->redirect($origin);
//            }
//
//            return $this->redirectToRoute('home');
//        } else {
//            $response = $this->apiMiddleware->get(ApiMiddleware::ROUTE_AUTH_EDUID, ['redirect' => $this->generateUrl('register')]);
//            $responseData = json_decode($response->getBody()->getContents(), true);
//            return $this->redirect($responseData['wayf_url']);
//        }
//
//    }

    /**
     * @param SessionInterface $session
     * @return bool
     * @throws \Exception
     */
    private function authDataReady(SessionInterface $session)
    {
        $authData= $session->get('auth_data');
        if ($authData && $authData['token_type'] === 'Bearer') {

            $expiresDate = (new \DateTime($authData['expires_at']));
            $now = (new \DateTime())->getTimestamp();
            return $expiresDate->getTimestamp() > $now;
        }
        return false;
    }

    /**
     * @Route("/logout", name="logout")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function logout(Request $request)
    {
        $request->getSession()->set('user_data', null);
        $request->getSession()->set('auth_data', null);
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/", name="home")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function index(Request $request)
    {
        if($this->authDataReady($request->getSession()))
        {
            return $this->render('login/indexLogged.html.twig', [
                'userData' => $request->getSession()->get('user_data'),
            ]);
        }

        return $this->render('login/index.html.twig');
    }

}
