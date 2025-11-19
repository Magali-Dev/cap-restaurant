<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Formulaire de réservation - Création de réservation de table en ligne
 * 
 * Ce formulaire permet aux clients de réserver une table avec :
 * - Informations personnelles validées (prénom, nom, téléphone, email)
 * - Détails de la réservation (nombre de personnes, date, heure)
 * - Informations complémentaires (allergies, message spécial)
 * - Validation avancée avec contraintes Regex pour la sécurité
 * - Génération automatique des créneaux horaires disponibles
 * 
 * Les créneaux horaires sont générés automatiquement pour :
 * - Le service du midi : 12h00 à 14h00 par pas de 30 minutes
 * - Le service du soir : 19h00 à 22h30 par pas de 30 minutes
 * 
 * Contrôles de validation : format email, téléphone, caractères autorisés,
 * date dans le futur, et limites de longueur pour tous les champs
 * Sécurité renforcée : validation stricte, protection XSS, limites de longueur
 */
class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, [
                'label' => 'Prénom *',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prénom est obligatoire.']),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Le prénom doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères.'
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÖØ-öø-ÿ\s\'-]+$/u',
                        'message' => 'Le prénom ne peut contenir que des lettres, espaces, apostrophes ou tirets.',
                    ]),
                ],
                'attr' => [
                    'minlength' => 2,
                    'maxlength' => 50,
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom *',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom est obligatoire.']),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.'
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
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone *',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le téléphone est obligatoire.']),
                    new Assert\Length([
                        'min' => 10,
                        'max' => 15,
                        'minMessage' => 'Le numéro doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le numéro ne peut pas dépasser {{ limit }} caractères.'
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
                'label' => 'Email *',
                'required' => true,
                'attr' => [
                    'placeholder' => 'exemple@domaine.com',
                    'maxlength' => 255,
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'email est obligatoire.']),
                    new Assert\Email([
                        'message' => 'Veuillez saisir un email valide.',
                        'mode' => 'strict',
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'L\'email ne peut pas dépasser {{ limit }} caractères.'
                    ]),
                ],
            ])
            ->add('nombrePersonnes', ChoiceType::class, [
                'label' => 'Nombre de personnes *',
                'choices' => array_combine(range(1, 14), range(1, 14)),
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nombre de personnes est obligatoire.']),
                    new Assert\Choice([
                        'choices' => range(1, 14),
                        'message' => 'Veuillez choisir un nombre de personnes valide.'
                    ]),
                ],
            ])
            ->add('dateReservation', DateType::class, [
                'label' => 'Date *',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'datepicker',
                    'min' => (new \DateTime())->format('Y-m-d'),
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La date est obligatoire.']),
                    new Assert\GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'Vous ne pouvez pas réserver dans le passé.',
                    ]),
                    new Assert\LessThanOrEqual([
                        'value' => '+3 months',
                        'message' => 'Vous ne pouvez pas réserver au-delà de 3 mois.',
                    ]),
                ],
            ])
            ->add('heureReservation', ChoiceType::class, [
                'label' => 'Heure *',
                'choices' => $this->getCreneaux(),
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'heure est obligatoire.']),
                    new Assert\Choice([
                        'choices' => array_keys($this->getCreneaux()), 
                        'message' => 'Veuillez choisir une heure valide.'
                    ]),
                ],
            ])
            ->add('allergie', TextareaType::class, [
                'label' => 'Allergies',
                'required' => false,
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Ex: fruits à coque, crustacés, gluten, lactose ...',
                    'maxlength' => 255,
                ],
                'constraints' => [
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Les informations sur les allergies ne peuvent pas dépasser {{ limit }} caractères.'
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-Z0-9À-ÖØ-öø-ÿ\s\',\.\-]+$/u',
                        'message' => 'Caractères non autorisés dans le champ allergies.',
                    ]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message complémentaire',
                'required' => false,
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Ex : anniversaire, demande spéciale, etc.',
                    'maxlength' => 1000,
                ],
                'constraints' => [
                    new Assert\Length([
                        'max' => 1000,
                        'maxMessage' => 'Le message ne peut pas dépasser {{ limit }} caractères.'
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-Z0-9À-ÖØ-öø-ÿ\s\',\.\-!?]+$/u',
                        'message' => 'Caractères non autorisés dans le message.',
                    ]),
                ],
            ]);
    }

    private function getCreneaux(): array
    {
        $creneaux = [];

        // Créneaux midi : 12:00 → 14:00 tous les 30 min
        $current = new \DateTime('12:00');
        $end = new \DateTime('14:00');
        while ($current <= $end) {
            $creneaux[$current->format('H:i')] = $current->format('H:i');
            $current->modify('+30 minutes');
        }

        // Créneaux soir : 19:00 → 22:30 tous les 30 min
        $current = new \DateTime('19:00');
        $end = new \DateTime('22:30');
        while ($current <= $end) {
            $creneaux[$current->format('H:i')] = $current->format('H:i');
            $current->modify('+30 minutes');
        }

        return $creneaux;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'reservation_form',
        ]);
    }
}