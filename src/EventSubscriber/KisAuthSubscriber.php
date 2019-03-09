<?php
/**
 * Created by PhpStorm.
 * User: rockuo
 * Date: 06.03.19
 * Time: 21:47
 */

namespace App\EventSubscriber;


use App\Controller\AuthenticatedController;
use App\Exception\RedirectException;
use App\Service\ApiMiddleware;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class KisAuthSubscriber implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var ApiMiddleware
     */
    private $apiMiddleware;

    /**
     * KisAuthSubscriber constructor.
     * @param UrlGeneratorInterface $router
     * @param ApiMiddleware $apiMiddleware
     */
    public function __construct(UrlGeneratorInterface $router, ApiMiddleware $apiMiddleware)
    {
        $this->router = $router;
        $this->apiMiddleware = $apiMiddleware;
    }


    /**
     * @param FilterControllerEvent $event
     * @throws RedirectException
     * @throws \Exception
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        $request = $event->getRequest();
        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof AuthenticatedController) {
            $authData = $request->getSession()->get('auth_data');

            if($request->get('_route') === 'login')
            {
                return;
            }

            if (!($authData && $authData['token_type'] === 'Bearer')) {
                $this->throwLoginRedirect($request);
            }

            $expiresDate = (new \DateTime($authData['expires_at']));
            $expiresDate->sub(new \DateInterval('PT10M'));

            $now = (new \DateTime())->getTimestamp();
            if ($expiresDate->getTimestamp() <= $now)
            {
                if(!$this->apiMiddleware->refreshToken())
                {
                    $this->throwLoginRedirect($request);
                }
            }

            //todo validovat čas tokenu, řešit obnovení atp
        }
    }

    /**
     * @param Request $request
     * @throws RedirectException
     */
    protected function throwLoginRedirect(Request $request)
    {
        if($request->getMethod() === Request::METHOD_GET)
        {
            $request->getSession()->set('origin', $request->getUri());
        }
        throw new RedirectException(
            new \Symfony\Component\HttpFoundation\RedirectResponse($this->router->generate('login'))
        );
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if ($exception instanceof RedirectException) {
            $event->setResponse($exception->getRedirectResponse());
            $event->stopPropagation();
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}