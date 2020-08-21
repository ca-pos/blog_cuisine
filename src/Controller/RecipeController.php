<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    /**
     * ====== AFFICHE LA LISTE DES RECETTES ======
     * 
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
     * ====== CRÉATION D'UNE RECETTE ======
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
        // il faudra ensuite créer dans administration un outil de validation manuelle de la recette avant insertion dans la liste
        if( $form->isSubmitted() && $form->isValid()) {

            $recipe->setAuthor($this->getUser());

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
     * ====== MODIFICATION D'UNE RECETTE =====
     * 
     * @Route("recipes/{slug}/edit", name="recipes_edit")
     * @Security("is_granted('ROLE_USER') and user === recipe.getAuthor()", message = "Vous n'êtes pas l'auteur de cette recette, vous ne pouvez pas la modifier")
     * 
     * @param Recipe $recipe
     * @param Request $request
     * @param EntityManagerInterface $manager
     * 
     * @return Response
     */

    public function edit( Recipe $recipe, Request $request, EntityManagerInterface $manager) {

        $form = $this->createForm(RecipeTYpe::class, $recipe);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $recipe->setAuthor($this->getUser());
            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
            'success',
            'La modification de la recette a bien été soumise'
            );

            return $this->redirectToRoute('recipes_show', [
                'slug' => $recipe->getSlug()
                ]);
        }

        return $this->render('recipe/edit.html.twig', [
                'form' => $form->createView(),
                'recipe' => $recipe,
            ]);
    }
    /**
     * ====== AFFICHE UNE RECETTE ======
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

    /**
     * ====== SUPPRIME UNE RECETTE ======
     * 
     *  @Route("recipes/{slug}/delete", name="recipes_delete")
     * @Security("is_granted('ROLE_USER') and user === recipe.getAuthor()", message = "Vous n'êtes pas l'auteur de cette recette, vous ne pouvez pas la supprimer")
     * 
     * @param EntityManagerInterface $manager
     * 
     * @return Response
     * 
     */

     public function delete( Recipe $recipe, EntityManagerInterface $manager ) {
        $manager->remove( $recipe );
        $manager->flush();

        $this->addFlash(
        '	success',
        '	La recette {recipe.name} a bien été supprimée'
        );

        return $this->redirectToRoute('homepage');
     }
}
