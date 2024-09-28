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
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;

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
                "constraints"=>[
                    new NotBlank(),
                    new NotNull(),
                    new Regex('#^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d\w\W]{8,30}$#','Le mot de passe doit contenir au moins une minuscule, une majuscule et un chiffre')
                ],
                "attr"=>[
                    'minLength' => 8,
                    'maxLength' => 30,
                    "pattern" => "^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)[a-zA-Z\\d\w\W]{8,30}$",
                ],
                "help"=>"Le mot de passe doit contenir au moins une minuscule, une majuscule et un chiffre"
            ])
            ->add('code',TextType::class,[
                "required" => false,
                'mapped' => false,
                "constraints"=>[
                    new Length(exactly: 8,exactMessage: 'Le code doit avoir {{ limit }} caractères')
                ],
                "attr"=>[
                    'length' => 8,
                ],
                "help"=> "Le code doit être de 8 caractères alphanumériques. Laissez vide pour une génération aléatoire du code"
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
