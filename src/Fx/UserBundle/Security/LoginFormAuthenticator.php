<?php

namespace Fx\UserBundle\Security;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Fx\UserBundle\Form\LoginType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var EntityManager
     */

    private $em;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(FormFactoryInterface $formFactory, EntityManagerInterface $em, RouterInterface $router, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function supports(Request $request)
    {
        if($request->attributes->get('_route') === 'fx_user_login' && $request->isMethod('POST')) {
            return true;
        }

        return false;
    }

    public function getCredentials(Request $request)
    {
        $form = $this->formFactory->create(LoginType::class);
        $form->handleRequest($request);
        $data = $form->getData();

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $data['_usernameOrEmail']
        );

        return $data;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {

        $usernameOrEmail = $credentials['_usernameOrEmail'];

        try {
            $user = $this->em->getRepository('FxUserBundle:User')->findOneByUsernameOrEmail($usernameOrEmail);
        } catch (NoResultException $e) {
            return null;
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $password = $credentials['_password'];

        if ($this->passwordEncoder->isPasswordValid($user, $password)) {
            return true;
        }

        return false;
    }

    protected function getLoginUrl()
    {
        return $this->router->generate('fx_user_login');
    }

    protected function getDefaultSuccessRedirectUrl()
    {
        return $this->router->generate('homepage');
    }
}