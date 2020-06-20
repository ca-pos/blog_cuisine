<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
}
