<?php
namespace CoreBundle\EventListener;

use CoreBundle\Service\RedirectUserDemoMode;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RedirectUserDemoModeListener
{

    private $router;
    private $redirectUserDemoMode;

    public function __construct(RouterInterface $router,
                                RedirectUserDemoMode $redirectUserDemoMode
)
    {
        $this->router = $router;
        $this->redirectUserDemoMode = $redirectUserDemoMode;
    }

    public function onKernelRequest(GetResponseEvent $event){

        if($event->isMasterRequest()) {

            $currentRoute = $event->getRequest()->attributes->get('_route');

            if (!$this->isDebugToolbarOrProfilerRequest($currentRoute)) {

                $requestMethod = $event->getRequest()->getMethod();

                if ($this->redirectUserDemoMode->hasUserToBeRedirected($currentRoute, $requestMethod)) {
                    $routeToRedirectUserTo = $this->redirectUserDemoMode->getRouteToRedirectUserTo();
                    $response = new RedirectResponse($this->router->generate($routeToRedirectUserTo));
                    $event->setResponse($response);
                }
            }
        }
    }

    private function isDebugToolbarOrProfilerRequest($route) {
        if ($route === '_wdt' || $route === '_profiler') {
            return true;
        }

        return false;
    }

}