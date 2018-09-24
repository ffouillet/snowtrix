<?php
namespace Fx\UserBundle\EventListener;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Fx\UserBundle\Entity\User;

/*
 * Redirect user to homepage if he is logged, don't have Admin Role,
 * and try to access following routes :
 * - fx_user_login
 * - fx_user_registration
 * - fx_user_forgotten_password
 */
class RedirectUserListener
{
    private $tokenStorage;
    private $authChecker;
    private $router;

    public function __construct(TokenStorageInterface $tokenStorage,
                                AuthorizationCheckerInterface $authChecker, RouterInterface $router)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authChecker = $authChecker;
        $this->router = $router;
    }

    public function onKernelRequest(GetResponseEvent $event){

        if($event->isMasterRequest()) {

            $currentRoute = $event->getRequest()->attributes->get('_route');

            if (!$this->isDebugToolbarOrProfilerRequest($currentRoute) && $this->isUserLogged() && !$this->isUserAdmin()) {

                if ($this->isAuthenticatedUserOnAnonymousPage($currentRoute)) {

                    $response = new RedirectResponse($this->router->generate('st_home'));
                    $event->setResponse($response);
                }
            }
        }
    }

    private function isUserLogged()
    {
        /* Test gives true even if the user is not logged in via "remember me cookie"
         * Not as 'IS_AUTHENTICATED_REMEMBERED'
         * https://symfony.com/doc/3.4/security.html#checking-to-see-if-a-user-is-logged-in-is-authenticated-fully
         */
        if ($this->authChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return true;
        }

        return false;
    }

    private function isUserAdmin(){
        if ($this->authChecker->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return false;
    }

    private function isDebugToolbarOrProfilerRequest($route) {
        if ($route === '_wdt' || $route === '_profiler') {
            return true;
        }

        return false;
    }

    private function isAuthenticatedUserOnAnonymousPage($currentRoute)
    {
        return in_array(
            $currentRoute,
            ['fx_user_login', 'fx_user_registration', 'fx_user_forgotten_password']
        );
    }
}