<?php

namespace Fx\UserBundle\Controller;

use Fx\UserBundle\Entity\User;
use Fx\UserBundle\Form\LoginType;
use Fx\UserBundle\Form\UserType;
use Fx\UserBundle\Form\ForgottenPasswordType;
use Fx\UserBundle\Form\ResetPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class SecurityController extends Controller
{
    /**
     * @Route("/login", name="fx_user_login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginType::class, array('username' => $lastUsername));

        dump($form);

        return $this->render('fx/security/login.html.twig',
            array(
                'form' => $form->createView(),
                'error' => $error,
        ));
    }

    /**
     * @Route("/register", name="fx_user_registration")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // Password encoding (also possible to do this via doctrine listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // Saving the user
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // Flash message : success
            $this->addFlash(
                'registrationInfo',
                'Merci ! Votre inscription a bien été prise en compte. Vous pouvez dès à présent vous connecter.'
            );

            return $this->redirectToRoute('fx_user_login');
        }

        return $this->render(
            'fx/security/register.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/logout", name="fx_user_logout")
     */
    public function logoutAction()
    {

    }

    /**
     * @Route("/forgotten-password", name="fx_forgotten_password")
     */
    public function forgottenPasswordAction(Request $request)
    {
        $form = $this->createForm(ForgottenPasswordType::class);

        $formResult = $this->handleForgottenPasswordForm($request, $form);

        return $this->render('fx/security/forgotten_password.html.twig',
            array('form' => $form->createView(),
                'formResult' => $formResult));
    }

    private function handleForgottenPasswordForm(Request $request, Form $form) {

        $form->handleRequest($request);

        $formResult = [];
        $formResult['success'] = false;
        $formResult['errors'] = [];

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $userEmailOrUsername = $form->getData()['userEmailOrUsername'];

            try {
                $user = $em->getRepository('FxUserBundle:User')->findOneByUsernameOrEmail($userEmailOrUsername);
            } catch (\Exception $e) {
                $formResult['errors'][] = "Le nom d'utilisateur ou l'adresse email que vous avez saisi ne correspondent à aucun utilisateur.";
            }

        } else {
            $formResult['success'] = true;
        }

        dump($formResult);

        return $formResult;
    }

    /**
     * @Route("/reset_password", name="fx_reset_password")
     */
    public function resetPasswordAction() {

    }
}