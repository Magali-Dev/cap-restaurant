<?php
namespace App\Form;

use App\Entity\Supplement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Formulaire Supplement - Gestion des suppléments pour l'administration
 * 
 * Ce formulaire permet aux administrateurs de créer et modifier des suppléments :
 * - Nom du supplément (ex: "Extra fromage", "Champignons", "Jambon")
 * - Prix du supplément avec précision de 2 décimales
 * 
 * Interface simple pour l'ajout d'ingrédients optionnels que les clients
 * peuvent sélectionner pour personnaliser leurs pizzas
 * Utilisé dans l'interface d'administration pour la gestion du catalogue
 * Sécurité renforcée : validation des entrées, protection contre les injections
 * et limites de valeurs pour garantir l'intégrité des données
 */
class SupplementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du supplément *',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le nom du supplément est obligatoire.',
                    ]),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÖØ-öø-ÿ0-9\s\-\']+$/u',
                        'message' => 'Le nom ne peut contenir que des lettres, chiffres, espaces, tirets et apostrophes.',
                    ]),
                ],
                'attr' => [
                    'minlength' => 2,
                    'maxlength' => 50,
                    'pattern' => '^[a-zA-ZÀ-ÖØ-öø-ÿ0-9\\s\\-\\\']+$',
                ],
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix (€) *',
                'scale' => 2,
                'attr' => [
                    'min' => 0.01,
                    'max' => 999.99,
                    'step' => 0.01,
                    'placeholder' => '0.00',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le prix est obligatoire.',
                    ]),
                    new Assert\Positive([
                        'message' => 'Le prix doit être supérieur à 0.',
                    ]),
                    new Assert\Range([
                        'min' => 0.01,
                        'max' => 999.99,
                        'minMessage' => 'Le prix doit être d\'au moins {{ limit }} €.',
                        'maxMessage' => 'Le prix ne peut pas dépasser {{ limit }} €.',
                        'invalidMessage' => 'Le prix doit être un nombre valide.',
                    ]),
                    new Assert\Type([
                        'type' => 'numeric',
                        'message' => 'Le prix doit être un nombre.',
                    ]),
                ],
                'html5' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Supplement::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'supplement_form',
        ]);
    }
}