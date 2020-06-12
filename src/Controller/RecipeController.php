<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * permet la création d'une recette
     * 
     * @Route("/recipes/new", name="recipes_create")
     *
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager) {
        $recipe = new Recipe();

        $form = $this->createForm(RecipeType::class, $recipe);

        // récupérer les éléments de la requêtre, notamment le POST
        $form->handleRequest($request);

        // le if ci-dessous devrait être repris pour mettre la nouvelle recette en attente de validation.
        // suggestion : sauvegarder à part (dans une autre table ? dans la table principale en ajoutant un champ statut ?)
        // il faudrat ensuite créer dans administration un outil de validation manuelle de la recette avant insertion dans la liste
        if( $form->isSubmitted() && $form->isValid()) {
            $manager->persist($recipe);
            $manager->flush();

            // avant d'afficher la nouvelle recette, on prévient l'utilisateur que celle-ci a été prise en compte à l'aide d'un message flash
            $this->addFlash(
                'success',
                "Merci pour votre contribution. La recette a bien été prise en compte, elle devrait bientôt apparaître dans la liste des recettes"
            );

            // une fois la recette enregistrée, on l'affiche grace à la route recipes_show ci-desssous avec le paramêtre slug de la recette
            return $this->redirectToRoute('recipes_show', [
                'slug'=>$recipe->getSlug()
            ]);
        }

        return $this->render('recipe/new.html.twig',[
            'form' => $form->createView()
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
}
