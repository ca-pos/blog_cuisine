<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    /**
     * @Route("/recipes", name="recipes_index")
     */
    public function index(RecipeRepository $repo)
    {
         $recipes = $repo->findAll();

        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }
    /**
     * affichage d'une seule annonce
     * 
     * @Route("/recipes/{slug}", name="recipes_show")
     *
     * @return Response
     */

    public function show(Recipe $recipe) {

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe
        ]);
    }

    /*
    public function show($slug, RecipeRepository $repo) {
        // récupération de la recette correspondant au slug
        $recipe = $repo->findOneBySlug($slug);

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe
        ]);
    }
    */
}
