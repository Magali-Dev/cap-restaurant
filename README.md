# Le CAP Ristobar

![Statut](https://img.shields.io/badge/statut-WIP-yellow)
![Symfony](https://img.shields.io/badge/framework-Symfony-000000?logo=symfony)
![Bootstrap](https://img.shields.io/badge/UI-Bootstrap-563D7C?logo=bootstrap)
![License](https://img.shields.io/badge/license-Propri%C3%A9taire-red)

## 1. Description

**Le Cap Ristobar** est un restaurant festif situé à Poitier (France), mêlant cuisine italienne et française dans une ambiance conviviale et chaleureuse.

L’établissement propose des pizzas maison, des **plats préparés avec des produits frais et locaux**, ainsi qu’un **bar à cocktails** et des **soirées animées** (blind-tests, karaokés, musique, événements privés).

Le site vise à offrir **une expérience fluide** pour **réserver une table** ou **commander des pizzas en ligne**.

👥 **Public cible** : clients locaux, touristes, utilisateurs mobiles, entreprises.  
🎓 **Contexte** : Projet réalisé dans le cadre du stage de fin de formation — développement complet (frontend, backend, back-office).

---

## 2. Table des matières

-   [Démo](#démo)
-   [Fonctionnalités](#fonctionnalités)
-   [Tech & outils](#tech--outils)
-   [Installation](#installation)
-   [Utilisation](#utilisation)
-   [Architecture & arborescence](#architecture--arborescence)
-   [Variables d’environnement](#variables-denvironnement)
-   [Base de données](#base-de-données)
-   [Tests](#tests)
-   [Accessibilité & responsive](#accessibilité--responsive)
-   [Performance & SEO](#performance--seo)
-   [Sécurité](#sécurité)
-   [Déploiement](#déploiement)
-   [Présentation examen](#présentation-pour-lexamen)
-   [Contribution](#contribution)
-   [Licence](#licence)

---

## 3. Démo

📸 **Captures d’écran**  
_(à insérer après réalisation des maquettes UI / MVP)_

-   `./public/screenshots/desktop.png` (à ajouter)
-   `./public/screenshots/mobile.png` (à ajouter)

🔗 **Lien démo** : [Figma - Le CAP Ristobar](https://www.figma.com/proto/5cVECR1FV9rN50KA5gP1Bw/CAP?node-id=53-381&t=U5fRs4pMjrcBmiLV-1&scaling=scale-down&content-scaling=fixed&page-id=0%3A1&starting-point-node-id=281%3A3713)

🎥 **Vidéo / GIF** : (à ajouter)

---

## 4. Fonctionnalités (priorisées pour l’examen)

✅ **MVP — livré d’ici Sprint 2 :**

-   Page d’accueil : bannière, galerie, vidéo, menu détaillé, horaires
-   Page “La Carte” : menu de la semaine et carte signature
-   Page “Réservation” : formulaire de réservation de table avec validation par mail ou SMS
-   Formulaire de Contact
-   Section "Évènements" : affiches des prochains évènements publics du CAP en dehors des évènement professionnels et privés
-   Google Maps : vue sur l'emplacement du CAP Ristobar sur Google Maps, ainsi que son adresse postale
-   Espace membre : inscription / connexion
-   Back-office admin : CRUD menus, réservations, commandes
-   Responsive

```diff
-   ? Optimisation lazy-load des images
-   ? Accessibilité (ARIA, contrastes, navigation clavier)
```

🔄 **Fonctionnalités à venir :**

-   Commande de pizzas en ligne (paiement Stripe sandbox)
-   Tableau de bord administrateur (Symfony CRUD)
-   Formulaire de privatisation (page “Services”)
-   Mentions légales, RGPD, CGU

---

## 5. Tech & outils

### ⚙️ Technologies principales

-   **Frontend** : Symfony (Twig, SSR) + Webpack Encore + Bootstrap 5
-   **Backend** : PHP 8.4 + Symfony 7 (MVC)
-   **Base de données** : MySQL (Doctrine ORM)
-   **Paiement (à venir)** : Stripe (Apple Pay, CB)
-   **Cartographie** : Google Maps (API)

```diff
!-   **Envoi mail / SMS** : Symfony Mailer + ? Twilio (optionnel)
```

### 🧰 Outils

-   **Gestion de dépendances** : npm, composer
-   **Build** : Webpack Encore
-   **Contrôle de version** : Git + GitHub
-   **Design / Wireframes** : Figma
-   **Documentation** : Notion
-   **Gestion projet** : Trello

---

## 6. Installation

### 🔧 Prérequis

-   PHP >= 8.4
-   Composer >= 2.8
-   Node.js >= 22.9
-   npm >= 10.8
-   MySQL
-   Git

### 💻 Installation locale

```bash
# Dans le Terminal :

# Cloner le projet
git clone https://github.com/Magali-Dev/cap-restaurant.git
cd cap-restaurant

# Installer les dépendances PHP
composer install

# Installer les dépendances front
npm install

# Compiler les assets
npm run dev

# Créer le fichier .env.local
cp .env .env.local
```

### 🪛 Configurer ensuite la base de données dans .env.local :

```bash
DATABASE_URL="mysql://root:@127.0.0.1:3306/cap_db?serverVersion=8.0.32&charset=utf8mb4"
```

Puis lancer les migrations dans le Terminal :

```bash
# Créer la base de données définie dans .env ou .env.local
php bin/console doctrine:database:create

# Exécuter les migrations pour mettre à jour la base selon les entités
php bin/console doctrine:migrations:migrate
```

## 7. Utilisation

### Lancer le serveur Symfony

```bash
# Dans le Terminal :
symfony server:start

# ou raccourci
# (stoppe les potentiels serveurs ouverts précédement et en ouvre un nouveau):
npm run start

# ou raccourci de débogage
# (cible tous les processus PHP-CGI, force leur arrêt, puis stoppe tous les potentiels serveurs ouverts et en ouvre un nouveau):
npm run start-kill
```

### Accéder au site

Accès local : http://localhost:8000 \

```diff
! Accès admin : /admin (si configuré)
```

### Stopper le serveur Symfony

```bash
# Dans le Terminal :
symfony server:stop

# ou raccourci :
npm run stop
```

## 8. Architecture & arborescence

```bash
cap-restaurant/
├── assets/                      # Ressources front-end
│   ├── controllers/             # Contrôleurs Stimulus
│   │   ├── csrf_protection_controller.js
│   │   └── hello_controller.js
│   ├── images/                  # Images du site
│   ├── styles/                  # Feuilles de style
│   │   ├── components/          # Composants réutilisables
│   │   │   ├── footer.css
│   │   │   └── navbar.css
│   │   ├── pages/               # Styles spécifiques aux pages
│   │   │   └── home.css
│   │   └── app.css
│   ├── videos/                  # Vidéos du site
│   └── app.js                   # Point d'entrée JS principal
│   └── bootstrap.js             # Initialisation Stimulus
│   └── controllers.json         # Configuration contrôleurs Stimulus
│
├── config/                      # Configuration Symfony
│   ├── packages/                # Configuration des bundles
│   ├── routes/                  # Définition des routes
│   ├── bundles.php
│   ├── preload.php
│   ├── routes.yaml
│   └── services.yaml
│
├── migrations/                  # Migrations base de données
│
├── public/                      # Dossier web public
│   ├── build/                   # Assets compilés (Webpack)
│   └── index.php                # Point d'entrée de l'application
│
├── src/                         # Code source PHP
│   ├── Controller/              # Contrôleurs MVC
│   │   └── HomeController.php
│   ├── DataFixtures/            # Données de test
│   │   └── AppFixtures.php
│   ├── Entity/                  # Entités Doctrine
│   ├── Repository/              # Repositories Doctrine
│   └── Kernel.php               # Noyau Symfony
│
├── templates/                   # Templates Twig
│   ├── home/                    # Templates page d'accueil
│   │   └── home.html.twig
│   ├── includes/                # Partials réutilisables
│   │   ├── _footer.html.twig
│   │   ├── _header.html.twig
│   ├── base.html.twig           # Template de base
│
├── translations/                # Fichiers de traduction
│
├── .editorconfig               # Configuration éditeur
├── .env                        # Variables d'environnement
├── .gitignore                  # Fichiers exclus de Git
├── composer.json               # Dépendances PHP
├── composer.override.yaml      # Configuration Docker Compose
├── composer.yaml               # Configuration Docker Compose
├── importmap.php               # Gestion des imports JS
├── package.json                # Dépendances JavaScript
├── phpunit.dist.xml            # Configuration PHPUnit
├── README.md                   # Documentation du projet
├── symfony.lock                # Lock des recipes Symfony
└── webpack.config.js           # Configuration Webpack
```

## 9. Variables d’environnement

| Variable              | Description               |
| --------------------- | ------------------------- |
| `DATABASE_URL`        | URL de connexion MySQL    |
| `APP_ENV`             | (dev / prod)              |
| `MAILER_DSN`          | SMTP pour envoi de mails  |
| `STRIPE_API_KEY`      | Clé API Stripe (paiement) |
| `GOOGLE_MAPS_API_KEY` | Intégration carte         |
| `APP_SECRET`          | Clé de sécurité Symfony   |

## 10. Base de données

### Tables principales

```diff
!-   `user` — comptes clients / admins
!-   `reservation` — réservations en ligne
!-   `dish` — plats, pizzas, desserts
!-   `order` — commandes en ligne
!-   `event` — soirées, animations (optionnel)
```

### Données de test

Fichiers fixtures disponibles :

```bash
# Dans le Terminal :
php bin/console doctrine:fixtures:load
```

## 11. Tests

### Commandes

```bash
# Dans le Terminal :

# Lancer les tests unitaires PHP avec PHPUnit :
php bin/phpunit

# Lancer les tests JS :
npm run test
```

### Types de tests

-   **Unitaires** : validation des formulaires
-   **Intégration** : flux réservation / commande
-   **E2E (Cypress)** : navigation mobile et desktop
-   **Accessibilité** : audits Lighthouse

## 12. Accessibilité & responsive

🟢 Conforme aux bonnes pratiques :

```diff
-   Navigation clavier (tabindex cohérent)
-   Contraste vérifié (WCAG AA)
-   Images avec attributs alt
-   Titres hiérarchiques (h1 → h3)
-   Responsive (Bootstrap grid + media queries)
-   Lazy loading des images
```

Tests réalisés avec Lighthouse, axe DevTools, simulateur mobile Chrome.

## 13. Performance & SEO

```diff
-   Lazy-loading images
-   Compression CSS/JS (Webpack Encore)
-   Meta tags dynamiques via Twig
-   Sitemap & robots.txt (à ajouter)
-   Objectif : chargement page < 2s
```

## 14. Sécurité

```diff
-   HTTPS (SSL activé en prod)
-   Protection CSRF (formulaires Symfony)
-   Validation stricte des inputs côté serveur
-   Authentification sécurisée (bcrypt)
-   Aucun secret stocké dans le repo Git
-   Politique RGPD complète (mentions + consentement cookie)
```

## 15. Déploiement

### Hébergement

```diff
-   Planethoster (mutualisé ou dédié)
-   Serveur LAMP compatible Symfony
```

### Procédure

```bash
## Build assets
npm run build

## Déploiement
git push production main
```

-   Configurer .env.prod.local (DB, Stripe, SMTP)
-   Générer clés SSL si besoin
-   Tester routes principales avant Go-live

## 16. Présentation pour l’examen

```diff
- ### Exemple :
```

⏱️ Durée totale : 6 à 8 minutes
| Étape | Temps | Contenu |
| --- | --- | --- |
| Contexte & objectifs |30 sec | Présenter Le Cap Ristobar |
|Démo fonctionnelle | 2–3 min | Menu + Réservation |
| Architecture & techno | 1 min | Symfony, Webpack, MySQL |
| Accessibilité & UX | 1 min | Responsive, contrastes, ARIA |
| Tests & déploiement | 1 min | Lighthouse + Go-live |
| Améliorations futures | 30 sec | Commande en ligne, Stripe |

```diff
- ### Fin d'exemple
```

### Points à montrer

```diff
-   ✅ Responsive (mobile / desktop)
-   ✅ Validation formulaire réservation
-   ✅ Message d’erreur (créneau complet)
-   ✅ Lazy load / SEO
-   ✅ CRUD admin rapide
```

## 17. Contact des développeuses

```diff
!> 👩‍💻 **Magali Bernardin-Bichet** \
!> 📍 ville, France \
!> 🗃️ [GitHub](https://github.com/Magali-Dev)
```

> 👩‍💻 **Madeline Ricateau** \
> 📍 Chasseneuil-du-Poitou, France \
> 🗃️ [GitHub](https://github.com/MadelineMorisset) \
> 🪢 [Linkedin](https://www.linkedin.com/in/madelinemorisset/)

## 19. Licence

    Projet réalisé dans le cadre d’une formation / examen.

    Le code source est destiné à un usage pédagogique et démonstratif.

    Tous les droits commerciaux et d’exploitation appartiennent à la SARL Le Cap Ristobar.

## 20. Annexes

-   📓 Cahier des charge (à venir)
-   🔗 Wireframes / Mockup Figma (à venir)
-   🗓️ Planning Agile : 5 sprints (10 semaines) (à venir)
