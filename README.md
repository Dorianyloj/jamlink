![Logo JamLink](https://via.placeholder.com/400x100/4A90E2/FFFFFF?text=🎵+JamLink)

# 🎵 JamLink

**La plateforme sociale qui connecte les musiciens**

[![Symfony](https://img.shields.io/badge/Symfony-7.2-000000.svg?style=flat&logo=symfony)](https://symfony.com/)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4.svg?style=flat&logo=php)](https://php.net/)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED.svg?style=flat&logo=docker)](https://docker.com/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

JamLink est une plateforme innovante qui permet aux musiciens de se connecter, former des groupes et partager leur passion musicale. Que vous soyez débutant ou professionnel, trouvez vos futurs partenaires musicaux et créez ensemble !

## ✨ Fonctionnalités principales

### 👤 Gestion des profils musiciens
- **Profils détaillés** : Nom, localisation, niveau d'expérience
- **Instruments** : Associez vos instruments et votre niveau de maîtrise
- **Portfolio** : Partagez vos créations musicales et médias

### 🎸 Groupes de musique
- **Création de groupes** : Fondez votre groupe avec description et styles musicaux
- **Gestion des membres** : Invitez et gérez les membres de votre groupe
- **Leadership** : Système de leader pour organiser le groupe
- **Limitation des membres** : Contrôlez la taille de votre formation

### 📢 Système d'annonces
- **Recrutement** : Publiez des annonces pour recruter de nouveaux membres
- **Recherche géographique** : Trouvez des musiciens près de chez vous
- **Filtres par instruments** : Recherchez des musiciens spécifiques
- **Gestion temporelle** : Annonces avec dates d'expiration

### 🔐 Sécurité & Authentification
- **JWT Authentication** : Système d'authentification sécurisé
- **Refresh tokens** : Sessions prolongées de manière sécurisée
- **API REST** : Interface complètement sécurisée

## 🚀 Technologies utilisées

| Technologie | Version | Description |
|-------------|---------|-------------|
| **Symfony** | 7.2 | Framework PHP moderne et robuste |
| **FrankenPHP** | Latest | Serveur PHP haute performance |
| **Doctrine ORM** | 3.3 | Mapping objet-relationnel |
| **JWT** | 3.1 | Authentification par tokens |
| **Docker** | Latest | Containerisation et déploiement |
| **MySQL** | 8.0 | Base de données relationnelle |

## 📋 Prérequis

Avant de commencer, assurez-vous d'avoir installé :

- **Docker** (v20.10+) et **Docker Compose** (v2.0+)
- **Git** pour cloner le repository
- **Minimum 4GB RAM** disponible pour les conteneurs

## 🛠️ Installation rapide

### 1️⃣ Clonage du projet
```bash
git clone https://github.com/votre-username/jamlink.git
cd jamlink
```

### 2️⃣ Construction et démarrage
```bash
# Construction des conteneurs
docker compose build --no-cache

# Démarrage des services
docker compose up --pull always -d --wait
```

### 3️⃣ Configuration de la base de données
```bash
# Création du schéma
docker compose exec php php bin/console doctrine:schema:update --force

# Chargement des données de démonstration
docker compose exec php php bin/console doctrine:fixture:load --no-interaction
```

### 4️⃣ Configuration JWT
```bash
# Génération des clés d'authentification
docker compose exec php php bin/console lexik:jwt:generate-keypair
```

### 5️⃣ Accès à l'application

🎉 **Votre application est prête !**

- **Application** : [http://localhost:8000](http://localhost:8000)

## 📚 API Endpoints

### 🔐 Authentification
```http
POST /api/auth/login      # Connexion utilisateur
POST /api/auth/refresh    # Renouvellement du token
```

### 👥 Utilisateurs
```http
GET    /api/users              # Liste des utilisateurs
GET    /api/users/{id}         # Profil utilisateur
POST   /api/users              # Création d'utilisateur
PUT    /api/users/{id}         # Mise à jour profil
GET    /api/users/profile      # Profil connecté
```

### 🎸 Groupes de musique
```http
GET    /api/music-groups           # Liste des groupes
GET    /api/music-groups/{id}      # Détails d'un groupe
POST   /api/music-groups           # Création de groupe
PUT    /api/music-groups/{id}      # Modification de groupe
POST   /api/music-groups/{id}/members  # Ajout de membre
```

### 📢 Annonces
```http
GET    /api/advertisements         # Liste des annonces
GET    /api/advertisements/{id}    # Détails d'une annonce
POST   /api/advertisements         # Création d'annonce
PATCH  /api/advertisements/{id}    # Modification d'annonce
```

### 🎵 Instruments
```http
GET    /api/instruments            # Liste des instruments disponibles
```

## 🧪 Données de test

Après avoir chargé les fixtures, vous pouvez utiliser ces comptes de test :

| Email | Mot de passe | Rôle |
|-------|--------------|------|
| `admin` | `password` | Musicien |

## 🔧 Commandes utiles

### Gestion Docker
```bash
# Arrêter tous les services
docker compose down

# Redémarrer les services
docker compose restart

# Voir les logs en temps réel
docker compose logs -f

# Accéder au conteneur PHP
docker compose exec php bash
```

### Développement Symfony
```bash
# Vider le cache
docker compose exec php php bin/console cache:clear

# Lancer les migrations
docker compose exec php php bin/console doctrine:migrations:migrate

# Générer une nouvelle entité
docker compose exec php php bin/console make:entity

# Voir les routes disponibles
docker compose exec php php bin/console debug:router
```

### Base de données
```bash
# Sauvegarder la base de données
docker compose exec mysql mysqldump -u root -p jamlink > backup.sql

# Restaurer la base de données
docker compose exec -i mysql mysql -u root -p jamlink < backup.sql
```

## 📁 Structure du projet

```
jamlink/
├── 🐳 Docker/
│   ├── compose.yaml              # Configuration Docker
│   ├── Dockerfile               # Image personnalisée
│   └── frankenphp/             # Configuration FrankenPHP
├── 🎵 src/
│   ├── Controller/             # Contrôleurs API
│   ├── Entity/                # Entités Doctrine
│   ├── Repository/            # Repositories
│   └── Serializer/           # Normalizers custom
├── 🔧 config/
│   ├── packages/             # Configuration des bundles
│   └── routes/              # Configuration des routes
├── 📊 migrations/           # Migrations de base de données
├── 🧪 JamLink/             # Collection Bruno (tests API)
└── 📚 docs/               # Documentation
```

## 🌍 Variables d'environnement

Créez un fichier `.env.local` pour personnaliser votre configuration :

```env
# Base de données
DATABASE_URL="mysql://user:password@localhost:3306/jamlink"

# JWT
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your-passphrase

# Upload
UPLOAD_DIR=%kernel.project_dir%/public/media
```

## 🎯 Fonctionnalités à venir

- [ ] 🔍 **Recherche avancée** : Filtres par style musical, localisation
- [ ] 💬 **Messagerie** : Chat entre musiciens
- [ ] 📅 **Calendrier** : Planning des répétitions
- [ ] 🎤 **Événements** : Organisation de concerts
- [ ] 📱 **Application mobile** : Version iOS/Android
- [ ] 🎧 **Player audio** : Écoute des démos intégrée

## 🤝 Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1. **Forkez** le projet
2. **Créez** une branche feature (`git checkout -b feature/amazing-feature`)
3. **Committez** vos changements (`git commit -m 'Add amazing feature'`)
4. **Pushez** vers la branche (`git push origin feature/amazing-feature`)
5. **Ouvrez** une Pull Request

## 🐛 Dépannage

### Problèmes courants

**Port déjà utilisé**
```bash
# Vérifier les ports utilisés
netstat -tulpn | grep :8000
# Modifier le port dans compose.yaml si nécessaire
```

**Problème de permissions**
```bash
# Réparer les permissions
sudo chown -R $USER:$USER .
chmod -R 755 var/
```

**Cache Symfony**
```bash
# Forcer la suppression du cache
docker compose exec php rm -rf var/cache/*
docker compose exec php php bin/console cache:clear
```

## 👨‍💻 Équipe

Développé avec ❤️ par l'équipe JamLink (moi seul)

---

**🎵 Rejoignez la communauté JamLink et créez la musique de demain ! 🎵**