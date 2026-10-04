# AB-Flash

Annuaire des agences de colis entre le Sénégal, les Comores et la France.

Application Laravel 11 complète avec gestion d'agences, vols, et espaces admin/partenaire.

## Installation

### Prérequis

- PHP 8.2+
- Composer
- SQLite (par défaut) ou MySQL
- Node.js (optionnel, pour Breeze - non utilisé dans cette version)

### Étapes d'installation

1. **Cloner le projet**
```bash
cd /Users/admin/Downloads/ab-flash-laravel
```

2. **Installer les dépendances**
```bash
composer install
```

3. **Configurer l'environnement**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurer la base de données**

Pour SQLite (par défaut, déjà configuré) :
```bash
# Rien à faire, SQLite est utilisé par défaut
```

Pour MySQL :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ab_flash
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Configurer le compte admin**
```env
ADMIN_NAME="Admin"
ADMIN_EMAIL="admin@abflash.local"
ADMIN_PASSWORD="your_secure_password"
```

6. **Exécuter les migrations et seeders**
```bash
php artisan migrate --seed
```

7. **Lancer le serveur de développement**
```bash
php artisan serve
```

L'application sera accessible sur http://127.0.0.1:8000

## Comptes de test

### Admin
- **Email** : admin@abflash.local
- **Mot de passe** : changeme123 (à modifier dans .env)
- **Accès** : http://127.0.0.1:8000/admin

### Agences de démonstration (seedées)
5 agences sont créées automatiquement avec le seeder :
- Moroni Express
- Baobab Cargo
- Karthala Colis
- Ylang Fret
- Teranga Envois

Ces agences sont au statut "approved" et visibles publiquement.

## Structure du projet

```
ab-flash-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── PublicController.php       # Pages publiques
│   │   │   ├── PartnerController.php      # Inscription partenaire
│   │   │   ├── AdminDashboardController.php # Espace admin
│   │   │   └── AgencyDashboardController.php # Espace agence
│   │   ├── Middleware/
│   │   │   ├── RoleMiddleware.php         # Middleware par rôle
│   │   │   └── SecurityHeaders.php       # En-têtes de sécurité
│   │   └── Requests/
│   │       └── PartnerRegistrationRequest.php # Validation inscription
│   ├── Models/
│   │   ├── User.php
│   │   ├── Agency.php
│   │   ├── AgencyRoute.php
│   │   ├── Flight.php
│   │   └── ActivityLog.php
│   ├── Policies/
│   │   ├── AgencyPolicy.php              # Politiques d'accès agence
│   │   └── FlightPolicy.php              # Politiques d'accès vol
│   └── Notifications/
│       └── NewPartnerNotification.php    # Notification admin
├── database/
│   ├── migrations/                       # Migrations de la base
│   ├── seeders/
│   │   ├── AdminSeeder.php               # Création admin
│   │   └── AgencySeeder.php              # Agences de démonstration
│   └── factories/                       # Factories pour tests
├── public/
│   ├── css/                             # Feuilles de style (copiées du statique)
│   │   ├── base.css
│   │   ├── hero.css
│   │   ├── components.css
│   │   ├── agences.css
│   │   ├── agence.css
│   │   ├── theme.css                     # Mode sombre/clair
│   │   ├── auth.css                      # Pages d'authentification
│   │   └── dashboard.css                 # Espaces admin/agence
│   └── js/
│       └── theme.js                      # Gestion du thème
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php             # Layout principal
│   │   ├── components/
│   │   │   ├── agence-card.blade.php     # Composant carte agence
│   │   │   └── vol-card.blade.php        # Composant carte vol
│   │   ├── home.blade.php                # Page d'accueil
│   │   ├── agences.blade.php             # Page recherche agences
│   │   ├── agence.blade.php              # Page détail agence
│   │   ├── auth/                         # Pages d'authentification
│   │   ├── admin/                        # Espace admin
│   │   └── agency/                       # Espace agence
└── routes/
    ├── web.php                           # Routes web
    └── auth.php                          # Routes d'authentification
```

## Fonctionnalités

### Pages publiques
- **Accueil** (`/`) : 4 agences approuvées, recherche rapide
- **Recherche** (`/agences`) : Filtrage par trajet (départ/destination)
- **Détail agence** (`/agences/{slug}`) : Informations agence + vols disponibles
- **Mode sombre/clair** : Toggle dans le header

### Inscription partenaire
- Formulaire complet avec validation
- Création user (role admin_agence) + agence (status pending)
- Vérification email obligatoire
- Notification automatique à l'admin
- Honeypot anti-spam

### Espace admin (`/admin`)
- Tableau de bord avec statistiques
- Gestion des agences (approuver, rejeter, suspendre, réactiver, supprimer)
- Gestion des utilisateurs
- Supervision de tous les vols
- Journal d'activité des actions admin

### Espace agence (`/admin-agence`)
- Tableau de bord (statut, vols à venir, kilos disponibles)
- Modification du profil agence
- CRUD des vols
- Action rapide : dupliquer un vol
- Gestion des trajets desservis
- Accès limité aux seules données de l'agence

## Sécurité

- **Autorisation** : Middleware par rôle + Policies Laravel
- **Validation** : Form Requests côté serveur
- **Mot de passe** : Hachage bcrypt, règles de mot de passe solides
- **CSRF** : Protection sur tous les formulaires
- **Rate limiting** : Sur login, register, password reset
- **En-têtes de sécurité** : CSP, X-Frame-Options, HSTS (production)
- **Échappement** : Blade `{{ }}` par défaut

## Tests

Exécuter les tests :
```bash
php artisan test
```

Tests disponibles :
- `AgencyAccessTest` : Vérifie les autorisations par rôle
- `Feature` : Tests fonctionnels des routes et controllers

## Configuration de production

1. **Modifier .env**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com

# Base de données MySQL recommandée
DB_CONNECTION=mysql
DB_HOST=votre-host
DB_DATABASE=ab_flash
DB_USERNAME=votre-user
DB_PASSWORD=votre-password
```

2. **Optimiser**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. **Configurer le serveur web**
- Nginx ou Apache avec PHP-FPM
- Rediriger tout vers public/index.php
- Configurer HTTPS avec Let's Encrypt

## Design

Le design est **identique pixel par pixel** au site statique original :
- Couleurs : vert #0F7A3E, jaune #FFC72C
- Police : Poppins (Google Fonts)
- Responsive mobile
- Animations CSS
- Aucun framework CSS (pas Bootstrap, pas Tailwind)

## Support

Pour toute question ou problème, contactez l'équipe de développement.
