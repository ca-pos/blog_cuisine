<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * connexion au site
     * 
     * @Route("/login", name="account_login")
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' =>  $username
        ]);
    }
    /**
     * déconnexion du site
     *
     * @Route("/logout", name="account_logout")
     * 
     * @return void
     */
    public function logout() {}

    /**
     * inscription sur le site
     * 
     * @Route("/account/register", name="account_register")
     * 
     * @return Response
     *
     * @return void
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder) {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);
        
        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user,  $user->getHash() );
            $user->setHash($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre inscription a bien été prise en compte. Merci de votre intérêt pour notre site."
            );

            return $this->redirectToRoute( 'account_login');
        }

        return $this->render("account/register.html.twig", [ 'form' => $form->createView()]);

    }

    /**
     * modification du profil utilisateur
     * @IsGranted("ROLE_USER")
     * 
     * @Route("/account/profile", name="account_profile")
     *
     * @return Response
     */
    public function profile(Request $request, EntityManagerInterface $manager) {

        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Vos informations ont bien été modifiées'
            );

            return $this->redirectToRoute( 'homepage');
        }

        return $this->render("account/profile.html.twig", ['form'=>$form->createView() ]);
    }

    /**
     * modifier le mdp
     * @IsGranted("ROLE_USER")
     * 
     * @Route("/account/password", name="account_password")
     *
     * @return Response
     */
    public function updatePassword(Request $request, EntityManagerInterface $manager,
    UserPasswordEncoderInterface $encoder) {
        $user = $this->getUser();
        $passwordUpdate = new PasswordUpdate();
    
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid() ) {
            if( !password_verify($passwordUpdate->getOldPassword(), $user->getHash())) {
                $form->get('oldPassword')->addError( new FormError( "Mot de passe incorrect" ));
            }
            else {
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword( $user, $newPassword );
                $user->setHash($hash);

                $manager->persist($user);
                $manager->flush();
    
                $this->addFlash(
                    'success',
                    "Le mot de passe a été modifié"
                );

                return $this->redirectToRoute('homepage');
            }

        }

        return $this->render("account/password.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * affiche la page de l'utilisateur connecté
     *
     * @Route("/account", name="account_index")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function myAccount() {
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }

}
