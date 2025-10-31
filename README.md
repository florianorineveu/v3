# Portfolio v3 - Florian "Ori" Neveu

[![CI](https://github.com/florianorineveu/v3/actions/workflows/ci.yaml/badge.svg)](https://github.com/florianorineveu/v3/actions/workflows/ci.yaml)
[![PHP Version](https://img.shields.io/badge/php-8.4-blue.svg)](https://www.php.net/)
[![Symfony Version](https://img.shields.io/badge/symfony-7.3-black.svg)](https://symfony.com/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg)](https://phpstan.org/)
[![Lighthouse Performance](https://img.shields.io/badge/Lighthouse-95%2B-success.svg)](https://developers.google.com/web/tools/lighthouse)

> Portfolio personnel développé avec Symfony 7.3, Stimulus et Webpack Encore.

## 📋 Table des matières

- [Prérequis](#-prérequis)
- [Installation](#-installation)
- [Développement](#-développement)
- [Tests et qualité](#-tests-et-qualité)
- [Déploiement](#-déploiement)
- [Structure du projet](#-structure-du-projet)
- [Technologies](#-technologies)
- [License](#-license)

## 🔧 Prérequis

- **Docker** et **Docker Compose** (recommandé)
- **PHP 8.4+** (si installation native)
- **Composer 2.x**
- **Node.js 20+** et **Yarn**
- **PostgreSQL 17**
- **Redis 7**

## 📦 Installation

### 1. Cloner le repository

```bash
git clone https://github.com/florianorineveu/v3.git
cd v3
```

### 2. Copier le fichier d'environnement

```bash
cp .env .env.local
# Éditer .env.local avec vos valeurs
```

### 3. Lancer l'environnement Docker

```bash
docker compose up -d
```

### 4. Installer les dépendances

```bash
# PHP
docker compose exec php composer install

# JavaScript
yarn install
```

### 5. Créer la base de données

```bash
docker compose exec php php bin/console doctrine:database:create
docker compose exec php php bin/console doctrine:migrations:migrate
```

### 6. Builder les assets

```bash
yarn build
```

Votre application est maintenant accessible sur **https://v3.local** (ou l'URL configurée).

## 🚀 Développement

### Commandes quotidiennes

```bash
# Lancer l'environnement Docker
docker compose up -d

# Arrêter l'environnement
docker compose down

# Voir les logs
docker compose logs -f php
docker compose logs -f nginx
```

### Frontend

```bash
# Mode développement (compile une fois)
yarn dev

# Mode watch (recompile automatiquement à chaque modification)
yarn watch

# Build production (minifié, optimisé)
yarn build

# Linter JavaScript
yarn lint
yarn lint:fix
```

### Backend

```bash
# Créer une entité
docker compose exec php php bin/console make:entity

# Créer un controller
docker compose exec php php bin/console make:controller

# Créer une migration
docker compose exec php php bin/console make:migration
docker compose exec php php bin/console doctrine:migrations:migrate

# Voir les routes
docker compose exec php php bin/console debug:router

# Vider le cache
docker compose exec php php bin/console cache:clear
```

### Base de données

```bash
# Créer la base
docker compose exec php php bin/console doctrine:database:create

# Supprimer et recréer
docker compose exec php php bin/console doctrine:database:drop --force
docker compose exec php php bin/console doctrine:database:create

# Mettre à jour le schéma
docker compose exec php php bin/console doctrine:schema:update --force

# Charger des fixtures (si configuré)
docker compose exec php php bin/console doctrine:fixtures:load
```

## ✅ Tests et qualité

### Tests PHP

```bash
# Lancer tous les tests
docker compose exec php vendor/bin/phpunit

# Tests avec couverture
docker compose exec php vendor/bin/phpunit --coverage-text

# Tests d'un fichier spécifique
docker compose exec php vendor/bin/phpunit tests/Unit/Entity/UserTest.php
```

### Analyse statique

```bash
# PHPStan (niveau 9)
vendor/bin/phpstan analyse

# PHP CS Fixer (vérification)
vendor/bin/php-cs-fixer fix --dry-run --diff

# PHP CS Fixer (correction automatique)
vendor/bin/php-cs-fixer fix

# Audit de sécurité Composer
composer audit
```

### Tests frontend

```bash
# ESLint
yarn lint
yarn lint:fix
```

### Lighthouse CI

```bash
# Tester les performances (nécessite Chrome)
yarn lighthouse
```

### Suite complète (avant un commit)

```bash
# 1. Linter et corriger
vendor/bin/php-cs-fixer fix
yarn lint:fix

# 2. Analyse statique
vendor/bin/phpstan analyse

# 3. Tests
docker compose exec php vendor/bin/phpunit

# 4. Build production
yarn build

# 5. Audit sécurité
composer audit
```

## 🌍 Déploiement

### Préprod

```bash
git push origin develop
# Le workflow deploy-preprod.yaml se déclenche automatiquement
```

### Production

```bash
# Créer une PR vers main
# Une fois mergée, le workflow deploy-prod.yaml se déclenche

# Lighthouse CI bloquera le merge si :
# - Performance < 95/100
# - Accessibility < 100/100
# - Best Practices < 100/100
# - SEO < 100/100
```

### Variables d'environnement

Configurez les secrets GitHub dans `Settings > Secrets > Actions` :

```
APP_SECRET
DATABASE_URL
MAILER_DSN
REDIS_URL
# ... autres secrets
```

## 📁 Structure du projet

```
.
├── assets/                    # Assets frontend (JS, SCSS)
│   ├── app.js                # Point d'entrée Webpack
│   ├── bootstrap.js          # Configuration Stimulus
│   ├── controllers/          # Controllers Stimulus
│   └── styles/               # Fichiers SCSS
├── config/                    # Configuration Symfony
│   ├── packages/             # Configuration des bundles
│   └── routes/               # Routes
├── docker/                    # Configuration Docker
│   ├── nginx/                # Config Nginx
│   └── php/                  # Config PHP-FPM
├── migrations/                # Migrations Doctrine
├── public/                    # Fichiers publics
│   ├── build/                # Assets buildés (généré)
│   └── index.php             # Front controller
├── src/                       # Code source PHP
│   ├── Controller/           # Controllers
│   ├── Entity/               # Entités Doctrine
│   ├── Repository/           # Repositories
│   └── ...
├── templates/                 # Templates Twig
│   └── base.html.twig        # Template de base
├── tests/                     # Tests PHPUnit
├── var/                       # Fichiers temporaires (cache, logs)
├── vendor/                    # Dépendances PHP
├── .github/                   # Workflows GitHub Actions
│   └── workflows/
│       ├── ci.yaml           # Tests et qualité
│       ├── deploy-preprod.yaml
│       └── deploy-prod.yaml
├── .lighthouserc.json        # Configuration Lighthouse
├── composer.json             # Dépendances PHP
├── package.json              # Dépendances JavaScript
└── webpack.config.js         # Configuration Webpack Encore
```

## 🛠️ Technologies

### Backend
- **Symfony 7.3** - Framework PHP
- **Doctrine ORM** - Gestion de base de données
- **PostgreSQL 17** - Base de données
- **Redis 7** - Cache et sessions
- **Messenger** - Gestion des queues asynchrones

### Frontend
- **Webpack Encore** - Build tool
- **Stimulus** - JavaScript framework léger
- **Turbo** - Navigation SPA
- **Sass/SCSS** - Préprocesseur CSS

### Qualité et tests
- **PHPUnit** - Tests unitaires et fonctionnels
- **PHPStan (niveau 9)** - Analyse statique
- **PHP CS Fixer** - Code style (Symfony + PSR-12)
- **ESLint** - Linter JavaScript
- **GrumpPHP** - Git hooks automatiques
- **Lighthouse CI** - Tests de performance web

### DevOps
- **Docker & Docker Compose** - Conteneurisation
- **GitHub Actions** - CI/CD
- **Nginx** - Serveur web
- **PHP-FPM** - Process manager PHP

## 🎯 Standards de qualité

Ce projet maintient des standards de qualité élevés :

- ✅ **PHPStan niveau 9** - Analyse statique stricte
- ✅ **PSR-12** - Code style Symfony
- ✅ **100% type coverage** - Typage strict PHP
- ✅ **Lighthouse scores** - 95+/100/100/100 (Performance/A11y/BP/SEO)
- ✅ **Git hooks** - Vérifications automatiques avant commit
- ✅ **CI/CD** - Tests automatiques sur toutes les PR

## 📄 License

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

---

## 👨‍💻 Auteur

**Florian "Ori" Neveu**

Développeur full-stack passionné par la qualité du code et les performances web.

- 🌐 Site: [https://florianorineveu.com](https://florianorineveu.com)
- 💻 GitHub: [@florianorineveu](https://github.com/florianorineveu)
- 💼 LinkedIn: [in/florianorineveu](https://linkedin.com/in/florianorineveu)
- 📸 Instagram: [@florianorineveu](https://instagram.com/florianorineveu)
- 🦋 Bluesky: [@florianorineveu](https://bsky.app/profile/florianorineveu)

---

**Ce projet est mon portfolio personnel.** Si vous souhaitez discuter de collaboration ou de questions techniques, n'hésitez pas à me contacter !
