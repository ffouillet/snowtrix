<?php

namespace Fx\UserBundle\Service;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class UserLogoutHandler implements LogoutSuccessHandlerInterface
{
    private $session;
    private $router;

    public function __construct(RouterInterface $router, SessionInterface $session)
    {
        $this->session = $session;
        $this->router = $router;
    }

    public function onLogoutSuccess(Request $request){

        $url = $this->router->generate('homepage');
        $url.= '?logout=success';

        $response = new RedirectResponse($url);

        return $response;

    }
}