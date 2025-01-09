<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# ParknSport 🏃‍♂️🅿️

## Description du Projet

ParknSport est une application web interactive conçue pour faciliter l'accès aux équipements sportifs d'Angers en fournissant des informations sur les parkings à proximité. Ce projet a été développé dans le cadre d'un hackathon utilisant les données ouvertes d'Angers Loire Métropole.

## Fonctionnalités Principales

### 1. Cartographie Interactive 🗺️
- Visualisation de tous les équipements sportifs sur une carte
- Interface intuitive avec des marqueurs distinctifs
- Système de zoom adaptatif avec ajustement automatique des distances de recherche

### 2. Recherche et Filtrage ⚡
- Recherche textuelle des équipements
- Filtrage par type d'équipement
- Filtrage par activité sportive
- Recherche par adresse avec autocomplétion

### 3. Gestion des Parkings 🚗
- Affichage des parkings (voitures et vélos) à proximité des équipements
- Informations en temps réel sur la disponibilité des places
- Calcul automatique des distances
- Différenciation visuelle entre parkings voitures et vélos

### 4. Fonctionnalités Utilisateur 👤
- Placement de points personnalisés sur la carte
- Recherche d'équipements dans un rayon personnalisable
- Vue détaillée des informations pour chaque équipement
- Réinitialisation facile de la vue

## Technologies Utilisées

- **Framework**: Laravel 10.x
- **Frontend**: 
  - Blade Templates
  - TailwindCSS pour le style
  - JavaScript vanilla pour l'interactivité
- **Cartographie**: Leaflet.js
- **Sources de Données**: 
  - API OpenData Angers Loire Métropole
  - Données en temps réel des parkings

## Choix Techniques

### 1. Architecture
- Application monolithique avec Laravel pour une mise en place rapide
- Utilisation de Blade pour un rendu côté serveur efficace
- Pas de framework JavaScript pour minimiser la complexité

### 2. Interface Utilisateur
- Design responsive et mobile-first
- Interface claire et intuitive avec TailwindCSS
- Indicateurs visuels de chargement et retours utilisateur

### 3. Performance
- Mise en cache des données statiques
- Chargement asynchrone des données de parking
- Optimisation des requêtes API

### 4. Accessibilité
- Marqueurs avec codes couleur distincts
- Messages d'erreur explicites
- Interface adaptable à différents appareils

## Installation

### Étapes d'installation

1. Cloner le repository
```bash
git clone https://github.com/votre-username/ParknSport.git
cd ParknSport
```

2. Installer les dépendances PHP avec Composer
```bash
composer install
```
Cette commande installe toutes les dépendances PHP nécessaires définies dans composer.json.

3. Installer les dépendances JavaScript avec NPM
```bash
npm install
```
Cette commande installe toutes les dépendances front-end nécessaires (TailwindCSS, etc.).

Modifier le fichier .env avec vos configurations locales (base de données, etc.).

4. Générer la clé d'application
```bash
php artisan key:generate
```
Cette commande génère une clé unique pour votre application, nécessaire pour le chiffrement.

5. Créer et migrer la base de données
```bash
php artisan migrate
```
Cette commande crée les tables nécessaires dans votre base de données.

6. Compiler les assets
```bash
npm run dev
```
Pour le développement, ou `npm run build` pour la production.

7. Lancer l'application
```bash
php artisan serve
```
L'application sera accessible à l'adresse http://localhost:8000
