<?php

namespace Fx\UserBundle\Controller;

use Doctrine\ORM\EntityManager;
use Fx\UserBundle\Entity\User;
use Fx\UserBundle\Form\LoginType;
use Fx\UserBundle\Form\UserType;
use Fx\UserBundle\Form\ForgottenPasswordType;
use Fx\UserBundle\Form\ResetPasswordType;
use Fx\UserBundle\Service\ForgottenPasswordKeyGenerator;
use Fx\UserBundle\Service\ResetUserPasswordUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class SecurityController extends Controller
{

    private const DELAY_BETWEEN_REPEATED_FORGOTTEN_PASSWORD_REQUEST = 300; // in seconds

    /**
     * @Route("/login", name="fx_user_login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginType::class, array('_usernameOrEmail' => $lastUsername));

        return $this->render('fx/security/login.html.twig',
            array(
                'form' => $form->createView(),
                'error' => $error,
        ));
    }

    /**
     * @Route("/register", name="fx_user_registration")
     */
    public function register(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /*
            * Password encoding is done with Doctrine Event Listener :
            * Fx\UserBundle\Security\HashPasswordListener
            */
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // Flash message : success
            $this->addFlash(
                'actionInfoSuccess',
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
     * @Route("/forgotten-password", name="fx_user_forgotten_password")
     */
    public function forgottenPasswordAction(Request $request,
                                            ForgottenPasswordKeyGenerator $forgottenPasswordKeyGenerator,
                                            ResetUserPasswordUrlGenerator $resetUserPasswordUrlGenerator)
    {
        $form = $this->createForm(ForgottenPasswordType::class);

        $this->handleForgottenPasswordForm($request, $form, $forgottenPasswordKeyGenerator, $resetUserPasswordUrlGenerator);

        return $this->render('fx/security/forgotten_password.html.twig',
            array('form' => $form->createView()));
    }

    private function handleForgottenPasswordForm(Request $request,
                                                 Form $form,
                                                 ForgottenPasswordKeyGenerator $forgottenPasswordKeyGenerator,
                                                 ResetUserPasswordUrlGenerator $resetUserPasswordUrlGenerator)
    {

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $userEmailOrUsername = $form->getData()['userEmailOrUsername'];

            try {
                $user = $em->getRepository('FxUserBundle:User')->findOneByUsernameOrEmail($userEmailOrUsername);
            } catch (\Exception $e) {
                $form->addError(new FormError('Le nom d\'utilisateur ou l\'adresse email que vous avez saisi ne correspondent à aucun utilisateur.'));
                return;
            }


            if($this->handleForgottenPasswordKeyGeneration($user, $em, $forgottenPasswordKeyGenerator)) {

            }

            $url = $resetUserPasswordUrlGenerator->generateResetPasswordUrl($user);

            // Send mail with reset password link
        }
    }

    private function handleForgottenPasswordKeyGeneration (User $user, $em, $forgottenPasswordKeyGenerator) {

        // We have to verify if the user don't have already submitted a forgotten password request.
        if($user->getForgottenPasswordKeyExpiresAt() != null) {

            $userForgottenPasswordKeyExpiresAtTimeStamp = $user->getForgottenPasswordKeyExpiresAt()->getTimestamp();
            $actualTimestamp = new \DateTime();
            $actualTimestamp = $actualTimestamp->getTimestamp();

            $secondsElapsedSinceLastRequest =
                $forgottenPasswordKeyGenerator::KEY_EXPIRATION_TIME
                - ($userForgottenPasswordKeyExpiresAtTimeStamp - $actualTimestamp);

            if ($secondsElapsedSinceLastRequest < self::DELAY_BETWEEN_REPEATED_FORGOTTEN_PASSWORD_REQUEST) {
                $totalTimeToWaitBeforeNewRequestInSeconds = self::DELAY_BETWEEN_REPEATED_FORGOTTEN_PASSWORD_REQUEST - $secondsElapsedSinceLastRequest;

                $flashMessage = $this->buildTimeRequiredToWaitBeforeNewForgottenPasswordFlashMessage($totalTimeToWaitBeforeNewRequestInSeconds);

                $this->addFlash(
                    'actionInfoError',
                    $flashMessage
                );

                return false;
            }

        }

        // Generate a key for the user in order for him to reset his password
        // And set an expiry time for this key (now + 1 hour)
        $forgottenPasswordKey = $forgottenPasswordKeyGenerator->generateForgottenPasswordKey($user);
        $forgottenPasswordKeyExpiresAt = $forgottenPasswordKeyGenerator->generateForgottenPasswordKeyExpirationDateTime();

        $user->setForgottenPasswordKey($forgottenPasswordKey);
        $user->setForgottenPasswordKeyExpiresAt($forgottenPasswordKeyExpiresAt);

        $em->flush();

        // Flash message : success
        $this->addFlash(
            'actionInfoSuccess',
            'Votre demande de nouveau mot de passe à bien été prise en compte, 
                vous recevrez un email concernant les instructions de réinitialisation de votre mot de passe dans quelques instants.'
        );

        return true;
    }

    private function buildTimeRequiredToWaitBeforeNewForgottenPasswordFlashMessage($totalTimeToWaitBeforeNewRequestInSeconds) {

        $timeToWaitBeforeNewRequestInMinutes = floor($totalTimeToWaitBeforeNewRequestInSeconds / 60);

        $flashMessage = "Impossible de soumettre une demande de nouveau mot de passe car une autre demande est déjà en cours. <br/>";
        $flashMessage.= "Merci de patienter ";

        // Minutes to display in flash message
        if($timeToWaitBeforeNewRequestInMinutes > 0) {
            if($timeToWaitBeforeNewRequestInMinutes > 1) {
                $flashMessage .= ($timeToWaitBeforeNewRequestInMinutes . " minutes");
            } else {
                $flashMessage .= ($timeToWaitBeforeNewRequestInMinutes . " minute");
            }
        }

        // Space between minutes and seconds if required.
        if($timeToWaitBeforeNewRequestInMinutes > 0 && $totalTimeToWaitBeforeNewRequestInSeconds % 60 != 0) {
            $flashMessage .= " et ";
        }

        // Seconds to display in flash message
        if($totalTimeToWaitBeforeNewRequestInSeconds % 60 != 0) {
            if($totalTimeToWaitBeforeNewRequestInSeconds % 60 > 1) {
                $flashMessage .= (($totalTimeToWaitBeforeNewRequestInSeconds % 60) . " secondes");
            } else {
                $flashMessage .= (" 1 seconde");
            }
        }

        $flashMessage.= " avant de pouvoir soumettre une nouvelle demande de nouveau mot de passe.";

        return $flashMessage;
    }

    /**
     * @Route("/reset_password/{forgottenPasswordKey}/{userId}",
     *     name="fx_reset_password",
     *     requirements={"forgottenPasswordKey"="\w{40}", "userId"="\d+"})
     */
    public function resetPasswordAction(Request $request, $forgottenPasswordKey, $userId) {

        $em = $this->getDoctrine()->getManager();

        $findOneUserCriterias = ['id' => $userId, 'forgottenPasswordKey' => $forgottenPasswordKey];

        $user = $em->getRepository('FxUserBundle:User')->findOneBy($findOneUserCriterias);

        if(null === $user)
            throw $this->createNotFoundException('Impossible de traiter votre demande. Demande de réinitialisation de mot de passe expiré ou utilisateur introuvable.');

        // Check if forgottenPasswordKeyExpiresAt is still valid
        if (new \DateTime() > $user->getForgottenPasswordKeyExpiresAt()) {
            $this->addFlash(
                'actionInfoError',
                'Le délai de votre demande de réinitialisation de mot de passe à expiré, 
                merci de soumettre une nouvelle demande de mot de passe.'
            );

            return $this->redirectToRoute('fx_user_forgotten_password');
        }

        // On peut maintenant lancer la procédure de reset de mot de passe.
        $form = $this->createForm(ResetPasswordType::class, $user);

        // If form submission is a success, redirect to fx_user_login_route
        if ($this->handleResetPasswordForm($request, $form, $em) ) {
           return $this->redirectToRoute('fx_user_login');
        }

        return $this->render('fx/security/reset_password.html.twig',
            array('form' => $form->createView()));

        dump($user);
        dump($findOneUserCriterias);
    }

    private function handleResetPasswordForm(Request $request, Form $form, EntityManager $em) {

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            /*
             * forgottenPasswordKey and forgottenPasswordKeyExpiresAt
             * have been setted to null in User entity, we only have to flush.
             */
            $em->flush();

            $this->addFlash(
                'actionInfoSuccess',
                'Merci ! Votre mot de passe a bien été modifié, vous pouvez dès à présent vous connecter.'
            );

            return true;
        }

        return false;
    }
}