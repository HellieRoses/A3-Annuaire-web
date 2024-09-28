<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ProfilUtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('visible', CheckboxType::class,[
                "label" => "Profil visible ?",
                'required' => false,
            ])
            ->add('code', TextType::class,[
                "label" => "Code Utilisateur",
                'mapped' => false,
                'required' => false,
                "constraints"=>[
                    new Length(exactly: 8,exactMessage: 'Le code doit avoir {{ limit }} caractères')
                ],
                "attr"=>[
                    'length' => 8,
                ],
                "help"=> "Le code doit être de 8 caractères alphanumériques. Laissez vide pour une génération aléatoire du code"
            ])
            ->add('name', TextType::class,[
                "label" => "Nom",
                "required" => false,
            ])
            ->add('first_name', TextType::class,[
                "label" => "Prenom",
                "required" => false,
            ])
            ->add('phone',TelType::class,[
                "label" => "Numéro de téléphone",
                "required" => false,
                "constraints"=>[
                    new Regex('/^[0-9]{10}+$/', "Le numéro de téléphone n'est pas du bon format")
                ],
                "attr"=>[
                    'length' => 10,
                    "pattern" => "//^[0-9]{10}+$//",
                ]
            ])
            ->add('nationality', CountryType::class,[
                "label" => "Nationalité",
                "required" => false,
            ])
            ->add('linkedin', TextType::class,[
                "label" => "Linkedin",
                "required" => false,
            ])
            ->add('profession', TextType::class, [
                "label" => "Profession",
                "required" => false,
            ])
            ->add("edition", SubmitType::class,[])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
