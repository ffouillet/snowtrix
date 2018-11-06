<?php

namespace UserBundle\Security;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use UserBundle\Form\LoginType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
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

    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    public function __construct(FormFactoryInterface $formFactory,
                                EntityManagerInterface $em,
                                RouterInterface $router,
                                UserPasswordEncoderInterface $passwordEncoder,
                                CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
        $this->csrfTokenManager = $csrfTokenManager;
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

        // CSRF valid token check
        $csrfToken = $request->request->get('login')['_token'];

        if (false === $this->csrfTokenManager->isTokenValid(new CsrfToken('csrf_token_login', $csrfToken))) {
            throw new InvalidCsrfTokenException('Jeton CSRF Invalide.');
        }

        // In order to have the login field autofilled with the last username typed
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
            $user = $this->em->getRepository('UserBundle:User')->findOneByUsernameOrEmail($usernameOrEmail);
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