# ParknSport 🏃‍♂️🅿️

## Description du Projet

ParknSport est une application web interactive conçue pour faciliter l'accès aux équipements sportifs d'Angers en fournissant des informations sur les parkings à proximité. Ce projet a été développé dans le cadre d'un hackathon utilisant les données ouvertes d'Angers Loire Métropole.

## Lien video Youtube explicative
https://youtu.be/cWdVg3UQjPQ?si=ahDLKL77PxrWZ69T

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
