<?php

namespace Fx\UserBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Fx\UserBundle\Entity\User;
use Fx\UserBundle\Form\ChangePasswordType;
use Fx\UserBundle\Form\Handler\ChangePasswordFormHandler;
use Fx\UserBundle\Form\Handler\EditProfileFormHandler;
use Fx\UserBundle\Form\Handler\ForgottenPasswordFormHandler;
use Fx\UserBundle\Form\Handler\RegisterFormHandler;
use Fx\UserBundle\Form\Handler\ResetPasswordFormHandler;
use Fx\UserBundle\Form\LoginType;
use Fx\UserBundle\Form\UserProfileEditType;
use Fx\UserBundle\Form\UserType;
use Fx\UserBundle\Form\UserEditType;
use Fx\UserBundle\Form\ForgottenPasswordType;
use Fx\UserBundle\Form\ResetPasswordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class SecurityController extends Controller
{

    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

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
    public function register(Request $request, RegisterFormHandler $registerFormHandler)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        if ($registerFormHandler->handle($request, $form, $user)) {
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
        # Magic ! Done by Symfony...
    }

    /**
     * @Route("/forgotten-password", name="fx_user_forgotten_password")
     */
    public function forgottenPasswordAction(Request $request, ForgottenPasswordFormHandler $forgottenPasswordFormHandler)
    {
        $form = $this->createForm(ForgottenPasswordType::class);

        $forgottenPasswordFormHandler->handle($request, $form);

        return $this->render('fx/security/forgotten_password.html.twig',
            array('form' => $form->createView()));
    }


    /**
     * @Route("/reset_password/{forgottenPasswordKey}/{userId}",
     *     name="fx_reset_password",
     *     requirements={"forgottenPasswordKey"="\w{40}", "userId"="\d+"})7
     */
    public function resetPasswordAction(Request $request, $forgottenPasswordKey, $userId, ResetPasswordFormHandler $resetPasswordFormHandler)
    {
        // Find the user. I don't use @ParamConverter because i need custom NotFoundException error message for this request.
        $findOneUserCriterias = ['id' => $userId, 'forgottenPasswordKey' => $forgottenPasswordKey];
        $user = $this->em->getRepository('FxUserBundle:User')->findOneBy($findOneUserCriterias);

        if(null === $user)
            throw $this->createNotFoundException('Erreur. Demande de réinitialisation de mot de passe expirée ou utilisateur introuvable.');

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
        if ($resetPasswordFormHandler->handle($request, $form) ) {
           return $this->redirectToRoute('fx_user_login');
        }

        return $this->render('fx/security/reset_password.html.twig',
            array('form' => $form->createView()));

    }

    /**
     * @Route("/edit-profile", name="fx_user_edit_profile")
     * @Security("is_granted('ROLE_USER')")
     * @param Request $request
     */
    public function editProfileAction(Request $request, EditProfileFormHandler $editProfileFormHandler, ChangePasswordFormHandler $changePasswordFormHandler) {

        $currentUser = $this->getUser();

        $editProfileForm = $this->createForm(UserProfileEditType::class, $currentUser);
        $editProfileFormHandler->handle($request, $editProfileForm, $currentUser);

        $changePasswordForm = $this->createForm(ChangePasswordType::class, $currentUser);
        $changePasswordFormHandler->handle($request, $changePasswordForm, $currentUser);

        return $this->render('fx/security/edit_profile.html.twig',
            array('editProfileForm' => $editProfileForm->createView(),
                'changePasswordForm' => $changePasswordForm->createView()
            ));

    }

}