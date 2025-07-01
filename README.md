# Mon Projet Symfony

## 🚀 Installation et Configuration

### Prérequis
- Docker et Docker Compose installés
- Git

### 🔧 Initialisation du projet

Après avoir cloné le projet, suivez ces étapes pour configurer votre environnement de développement :

#### 1. Cloner le projet
```bash
git clone [URL_DU_REPO]
cd [NOM_DU_PROJET]
```

#### 2. Configuration Docker
```bash
# Construction des conteneurs (sans cache)
docker compose build --no-cache

# Démarrage des services
docker compose up --pull always -d --wait
```

#### 3. Configuration de la base de données
```bash
# Mise à jour du schéma de base de données
docker compose exec php php bin/console doctrine:schema:update --force

# Chargement des fixtures (données de test)
docker compose exec php php bin/console doctrine:fixture:load
```

#### 4. Configuration JWT (Authentification)
```bash
# Génération des clés JWT
docker compose exec php bin/console lexik:jwt:generate-keypair
```

### ✅ Vérification de l'installation

Une fois toutes les commandes exécutées, votre application devrait être accessible à l'adresse :
- **Frontend** : http://localhost:8000
- **API** : http://localhost:8000/api

### 🛠️ Commandes utiles

#### Gestion des conteneurs
```bash
# Arrêter les services
docker compose down

# Redémarrer les services
docker compose restart

# Voir les logs
docker compose logs -f
```

#### Commandes Symfony
```bash
# Accéder au conteneur PHP
docker compose exec php bash

# Cache clear
docker compose exec php php bin/console cache:clear

# Migrations
docker compose exec php php bin/console doctrine:migrations:migrate
```

### 📁 Structure du projet
```
├── docker-compose.yml
├── Dockerfile
├── src/
├── config/
├── public/
├── templates/
└── README.md
```

### 🔑 Variables d'environnement

Assurez-vous de configurer vos variables d'environnement dans le fichier `.env.local` si nécessaire.

### 🐛 Dépannage

Si vous rencontrez des problèmes :
1. Vérifiez que Docker est bien démarré
2. Assurez-vous que les ports ne sont pas déjà utilisés
3. Consultez les logs avec `docker compose logs`

---

**Bon développement ! 🎉**