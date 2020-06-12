<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RecipeType extends AbstractType
{
    /**
     * retourne la configuration de base d'un champ
     *
     * @param string $label
     * @param string $placeholder
     * @return array
     */
    private function getConfiguration($label, $placeholder) {
        return [
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ]
            ];
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('author', TextType::class, $this->getConfiguration('Auteur de la recette', 'Votre pseudo'))
            ->add('name', TextType::class, $this->getConfiguration("Nom de la recette", "Entrez le nom de votre recette"))
            ->add('summary', TextType::class, $this->getConfiguration("Présentation", "Courte description votre recette (250 car. max) qui sera affiché dans la liste des recettes"))
            ->add('image', UrlType::class, $this->getConfiguration("Photo", "Entrez l'url d'une photo de votre plat"))
            ->add('ingredients', TextareaType::class, $this->getConfiguration("Ingrédients", "Si possible, précisez le nombre de convives et les quantités correspondantes"))
            ->add('method', TextareaType::class, $this->getConfiguration("Préparation", "Décrivez les étapes de la préparation"))
            ->add('notes', TextareaType::class, $this->getConfiguration("Commentaires", "Tous les commentaires qui vous semblent pertinents, p.ex. suggestion de vin, variantes de la recette, etc."))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
