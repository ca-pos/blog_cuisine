<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AccountType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                $this->getConfiguration("Courriel","Entrez votre nouvelle adresse électronique")
                )
            ->add(
                'avatar',
                UrlType::class,
                $this->getConfiguration("Photo de profil","Entrez l'url de votre nouvel avatar"))
            ->add(
                'introduction',
                TextareaType::class,
                $this->getConfiguration("Introduction","Entrez votre nouveau texte de présentation (au moins 50 caractères)")
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
