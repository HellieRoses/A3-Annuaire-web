<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InscriptionUtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('login',TextType::class,[
                'label' => 'Nom d\'utilisateur',
            ])
            ->add('email',EmailType::class,[
                'label' => 'Email',
            ])
            ->add('plainPassword',PasswordType::class,[
                'label' => 'Mot de passe',
                'mapped' => false,
            ])
            ->add('code',TextType::class,[
                "required" => false,
                'mapped' => false,
            ])
            ->add('visible',CheckboxType::class,[
                'label' => 'Profil visible ?',
                'required' => false,
            ])
            ->add('inscription', SubmitType::class,[])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
