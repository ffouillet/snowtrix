<?php

namespace Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

/**
 * LoggedInWebTestCase allows to simulate an user authentication so that the client
 * can access application's secured areas (depending on the user role).
 */
abstract class LoggedInWebTestCase extends WebTestCase
{
    protected $client;

    protected function logIn(Client $client, $firewallName, $firewallContext, $userRoles = ['ROLE_USER']){

        $session = $client->getContainer()->get('session');

        // Mock a user
        $user = $this->createMock('UserBundle\Entity\User');
        $user->method('getId')->willReturn(1);

        // This project use a Guard Authentication, the authenticated token is a PostAuthenticationGuardToken.
        $token = new PostAuthenticationGuardToken($user, $firewallName, $userRoles);

        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
    }
}
