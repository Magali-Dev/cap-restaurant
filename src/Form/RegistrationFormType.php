<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Email;
/**
 * Formulaire d'inscription utilisateur - Création de compte client
 * 
 * Ce formulaire permet la création d'un nouveau compte utilisateur avec :
 * - Champs obligatoires : nom, email, mot de passe (avec confirmation)
 * - Champs optionnels : prénom, numéro de téléphone
 * - Validation des données avec contraintes Symfony
 * - Vérification de la force du mot de passe (12 caractères minimum avec exigences de complexité)
 * - Acceptation obligatoire des conditions d'utilisation
 * 
 * Utilise le type RepeatedType pour la confirmation du mot de passe
 * et inclut des contraintes de sécurité avancées pour les mots de passe
 */
class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom *',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre nom',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\s\-]+$/u',
                        'message' => 'Le nom ne peut contenir que des lettres, espaces et tirets'
                    ])
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\s\-]*$/u',
                        'message' => 'Le prénom ne peut contenir que des lettres, espaces et tirets'
                    ])
                ],
            ])
            ->add('numeroTelephone', TelType::class, [
                'label' => 'Numéro de téléphone',
                'required' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[0-9\s\-\+\(\)\.]+$/',
                        'message' => 'Format de téléphone invalide. Caractères autorisés : chiffres, espaces, +, -, (), .'
                    ]),
                    new Length([
                        'min' => 10,
                        'max' => 20,
                        'minMessage' => 'Le numéro de téléphone doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le numéro de téléphone ne peut pas dépasser {{ limit }} caractères',
                    ])
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email *',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre email',
                    ]),
                    new Email([
                        'message' => 'L\'adresse email "{{ value }}" n\'est pas valide.',
                        'mode' => 'strict',
                    ]),
                    new Length([
                        'max' => 180,
                        'maxMessage' => 'L\'email ne peut pas dépasser {{ limit }} caractères',
                    ])
                ],
            ])
            ->add('motDePasse', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'first_options'  => [
                    'label' => 'Mot de passe *',
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez entrer un mot de passe',
                        ]),
                        new Length([
                            'min' => 12,
                            'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                            'max' => 4096,
                        ]),
                        new Regex([
                            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&\/#\-_])[A-Za-z\d@$!%*?&\/#\-_]/',
                            'message' => 'Le mot de passe doit contenir au moins : 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial (@$!%*?&/#_-)'
                        ])
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmation du mot de passe *',
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter nos conditions.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'registration_form',
        ]);
    }
}