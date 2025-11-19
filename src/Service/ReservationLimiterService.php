<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Service de limitation des réservations - Gestion des contraintes de réservation
 */
class ReservationLimiterService
{
    private string $configFile;
    private array $defaultConfig;
    private Filesystem $filesystem;

    public function __construct(ParameterBagInterface $params)
    {
        $projectDir = $params->get('kernel.project_dir');
        $this->configFile = $projectDir . '/var/reservation_limits.json';
        $this->filesystem = new Filesystem();
        $this->defaultConfig = [
            'online_enabled' => true,
            'disabled_hours' => [],
            'disabled_dates' => []
        ];
    }

    private function loadConfig(): array
    {
        if (!$this->filesystem->exists($this->configFile)) {
            $this->saveConfig($this->defaultConfig);
            return $this->defaultConfig;
        }

        try {
            $content = file_get_contents($this->configFile);
            $config = json_decode($content, true) ?? $this->defaultConfig;
            
            // Validation de la structure
            return array_merge($this->defaultConfig, $config);
            
        } catch (\Exception $e) {
            // En cas d'erreur de lecture, retourner la config par défaut
            return $this->defaultConfig;
        }
    }

    private function saveConfig(array $config): void
    {
        try {
            $this->filesystem->mkdir(dirname($this->configFile));
            
            // S'assurer que la structure est correcte
            $config = array_merge($this->defaultConfig, $config);
            
            file_put_contents($this->configFile, json_encode($config, JSON_PRETTY_PRINT));
            
        } catch (\Exception $e) {
            throw new \RuntimeException('Impossible de sauvegarder la configuration des limitations: ' . $e->getMessage());
        }
    }

    // --- Réservations en ligne ---
    public function isOnlineReservationEnabled(): bool
    {
        return (bool) ($this->loadConfig()['online_enabled'] ?? true);
    }

    public function setOnlineReservation(bool $enabled): void
    {
        $config = $this->loadConfig();
        $config['online_enabled'] = $enabled;
        $this->saveConfig($config);
    }

    // --- Heures désactivées ---
    public function getDisabledHours(): array
    {
        $hours = $this->loadConfig()['disabled_hours'] ?? [];
        return is_array($hours) ? array_filter($hours) : [];
    }

    public function setDisabledHours(array $hours): void
    {
        $config = $this->loadConfig();
        $config['disabled_hours'] = array_values(array_unique(array_filter($hours)));
        $this->saveConfig($config);
    }

    // --- Dates désactivées ---
    public function getDisabledDates(): array
    {
        $dates = $this->loadConfig()['disabled_dates'] ?? [];
        
        if (!is_array($dates)) {
            return [];
        }

        // Gestion du cas où c'est une chaîne CSV
        if (count($dates) === 1 && is_string($dates[0]) && str_contains($dates[0], ',')) {
            $dates = explode(',', $dates[0]);
        }

        $validDates = [];
        foreach ($dates as $date) {
            if (empty(trim($date))) continue;
            
            try {
                // Si c'est déjà un DateTime, le convertir en string
                if ($date instanceof \DateTimeInterface) {
                    $validDates[] = $date->format('Y-m-d');
                } else {
                    // Valider le format de date
                    $dateObj = new \DateTime(trim($date));
                    $validDates[] = $dateObj->format('Y-m-d');
                }
            } catch (\Exception $e) {
                // Ignorer les dates invalides
                continue;
            }
        }

        return array_unique($validDates);
    }

    public function setDisabledDates(array $dates): void
    {
        $validDates = [];
        
        foreach ($dates as $date) {
            if (empty($date)) continue;
            
            try {
                if ($date instanceof \DateTimeInterface) {
                    $validDates[] = $date->format('Y-m-d');
                } else {
                    $dateObj = new \DateTime($date);
                    $validDates[] = $dateObj->format('Y-m-d');
                }
            } catch (\Exception $e) {
                // Ignorer les dates invalides
                continue;
            }
        }

        $config = $this->loadConfig();
        $config['disabled_dates'] = array_values(array_unique($validDates));
        $this->saveConfig($config);
    }

    /**
     * Vérifie si une date et heure spécifiques sont autorisées
     */
    public function isDateTimeAllowed(\DateTimeInterface $date, \DateTimeInterface $time): bool
    {
        // Vérifier si les réservations en ligne sont activées
        if (!$this->isOnlineReservationEnabled()) {
            return false;
        }

        $dateString = $date->format('Y-m-d');
        $timeString = $time->format('H:i');

        // Vérifier les dates bloquées
        if (in_array($dateString, $this->getDisabledDates())) {
            return false;
        }

        // Vérifier les heures bloquées
        if (in_array($timeString, $this->getDisabledHours())) {
            return false;
        }

        return true;
    }

    public function getLimitationStatus(): array
    {
        return [
            'online_enabled' => $this->isOnlineReservationEnabled(),
            'disabled_hours' => $this->getDisabledHours(),
            'disabled_dates' => $this->getDisabledDates()
        ];
    }

    public function generateTimeSlots(): array
    {
        $slots = [];
        
        // Créneaux du midi
        $current = \DateTime::createFromFormat('H:i', '12:00');
        $end = \DateTime::createFromFormat('H:i', '14:00');
        while ($current <= $end) {
            $slots[] = $current->format('H:i');
            $current->modify('+30 minutes');
        }

        // Créneaux du soir
        $current = \DateTime::createFromFormat('H:i', '19:00');
        $end = \DateTime::createFromFormat('H:i', '22:30');
        while ($current <= $end) {
            $slots[] = $current->format('H:i');
            $current->modify('+30 minutes');
        }

        return $slots;
    }

    /**
     * Réinitialise toutes les limitations
     */
    public function resetAllLimitations(): void
    {
        $this->saveConfig($this->defaultConfig);
    }
}