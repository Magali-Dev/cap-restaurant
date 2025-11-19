<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Formulaire User - Édition du profil utilisateur
 * 
 * Ce formulaire permet la modification des informations utilisateur :
 * - Informations personnelles (nom, prénom, téléphone, email)
 * - Modification du mot de passe avec confirmation
 * - Champ informatif sur les caractères autorisés
 * 
 * Validation des mots de passe : minimum 12 caractères avec complexité, confirmation obligatoire
 * Sécurité renforcée : validation stricte des entrées, protection contre les injections
 * et contraintes avancées pour garantir l'intégrité des données utilisateur
 * Utilisé pour permettre aux utilisateurs de mettre à jour leur profil
 * et leurs informations de connexion en toute sécurité
 */
class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom *',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le nom est obligatoire.',
                    ]),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÖØ-öø-ÿ\s\'-]+$/u',
                        'message' => 'Le nom ne peut contenir que des lettres, espaces, apostrophes ou tirets.',
                    ]),
                ],
                'attr' => [
                    'minlength' => 2,
                    'maxlength' => 50,
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 50,
                        'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÖØ-öø-ÿ\s\'-]*$/u',
                        'message' => 'Le prénom ne peut contenir que des lettres, espaces, apostrophes ou tirets.',
                    ]),
                ],
                'attr' => [
                    'maxlength' => 50,
                ],
            ])
            ->add('numeroTelephone', TextType::class, [
                'label' => 'Numéro de téléphone',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'min' => 10,
                        'max' => 15,
                        'minMessage' => 'Le numéro doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le numéro ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^(\+33|0)[1-9](\d{2}){4}$/',
                        'message' => 'Le numéro de téléphone doit être un numéro français valide.',
                    ]),
                ],
                'attr' => [
                    'minlength' => 10,
                    'maxlength' => 15,
                    'pattern' => '^(\+33|0)[1-9](\d{2}){4}$',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail *',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'L\'email est obligatoire.',
                    ]),
                    new Assert\Email([
                        'message' => 'Veuillez saisir un email valide.',
                        'mode' => 'strict',
                    ]),
                    new Assert\Length([
                        'max' => 180,
                        'maxMessage' => 'L\'email ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => [
                    'maxlength' => 180,
                ],
            ])
            ->add('motDePasse', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'required' => true,
                'first_options' => [
                    'label' => 'Mot de passe *',
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                    'constraints' => [
                        new Assert\NotBlank([
                            'message' => 'Veuillez entrer un mot de passe',
                        ]),
                        new Assert\Length([
                            'min' => 12,
                            'minMessage' => 'Le mot de passe doit faire au moins {{ limit }} caractères',
                            'max' => 4096,
                        ]),
                        new Assert\Regex([
                            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&\/#\-_])[A-Za-z\d@$!%*?&\/#\-_]/',
                            'message' => 'Le mot de passe doit contenir au moins : 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial (@$!%*?&/#_-)'
                        ])
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe *',
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
            ])
            ->add('caracteresAutorises', TextType::class, [
                'label' => 'Caractères autorisés (info)',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Ce champ ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-Z0-9À-ÖØ-öø-ÿ\s\-,\.\(\)]+$/u',
                        'message' => 'Caractères non autorisés dans ce champ.',
                    ]),
                ],
                'attr' => [
                    'maxlength' => 255,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'user_profile_form',
        ]);
    }
}