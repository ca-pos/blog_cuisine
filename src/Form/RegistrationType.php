<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'pseudo',
                TextType::class,
                $this->getConfiguration("Identifiant", "Choisissez un pseudo")
                )
            ->add(
                'firstName',
                TextType::class,
                $this->getConfiguration("Prénom", "Entrez votre prénom usuel")
            )
            ->add(
                'lastName',
                TextType::class,
                $this->getConfiguration("Nom", "Entrez votre nom de famille")
            )
            ->add(
                'email',
                EmailType::class,
                $this->getConfiguration("Courriel", "Entrez votre adresse électronique")
                )
            ->add('avatar',
                UrlType::class,
                $this->getConfiguration("Photo de profil", "Entrez l'url de votre photo de profil")
                )
            ->add('hash',
                PasswordType::class,
                $this->getConfiguration("Mot de passe", "Choisissez un mot de passe de 8 caractères ou plus"),
                )
            ->add("passwordConfirm", PasswordType::class, $this->getConfiguration( "Confirmation du mot de passe", "Entrez à nouveau le mot de passe" ) )
            ->add('introduction',
                TextareaType::class,
                $this->getConfiguration("Introduction", "Présentez-vous brièvement (mais au moins 50 caractères !)")
                )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
