<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * 
     * affichage de la page profil d'un utilisateur
     * 
     * @Route("/user/{slug}", name="user_show")
     */
    public function index(User $user)
    {
        return $this->render('user/index.html.twig', [
            'user' =>$user,
        ]);
    }

    /**
     * affiche la liste des membres
     * 
     * @Route( "/users_list", name="users_list")
     *
     * @return void
     */
    public function users_list( UserRepository $repo) {

        $users = $repo->findAll();

        return $this->render('user/list.html.twig', [
            "users" => $users,
        ]);
    }
}
