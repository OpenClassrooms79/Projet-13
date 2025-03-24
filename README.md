# Mettez en place un site de e-commerce avec Symfony (projet 13)

Bienvenue sur le projet **Green Goodies** développé avec Symfony 7.  
Un site e-commerce avec gestion de panier et génération de commandes.

## 🛠️ Prérequis

- PHP >= 8.2
- Composer
- Symfony CLI
- MySQL ou MariaDB

## 1️⃣ Installation 🚀

Clonez le projet :

```bash
https://github.com/OpenClassrooms79/Projet-13.git
cd Projet-13
```

Installez les dépendances :

```bash
composer install --no-dev --optimize-autoloader
```

Créez un fichier `.env.local` et renseignez les variables suivantes :

```dotenv
APP_SECRET
DATABASE_URL
```

Créez la base et lancez les migrations :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

## 2️⃣ Créer des clés de chiffrement pour JWT ⚙️

```bash
php bin/console lexik:jwt:generate-keypair
```

## 3️⃣ Compilation des assets 🎨

```bash
php bin/console asset-map:compile --no-debug
php bin/console cache:warmup
```

## 4️⃣ Usage / Lancement 🌐

```bash
symfony server:start
```