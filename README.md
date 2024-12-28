# Symfony E-commerce Project

> [!IMPORTANT]
> ⚠️ **Pour avoir le rôle admin ou super admin, il faut décommenter la ligne correspondante dans le fichier `RegistrationController.php`.** ⚠️

## Contributeur(s)

> Jérôme ZHAO ([TerukiIIM](https://github.com/TerukiIIM))

## Description

Ce projet est une application e-commerce développée avec le framework Symfony. Il permet aux utilisateurs de parcourir une liste de produits, de consulter les détails des produits, d'ajouter des produits à leur panier, de passer des commandes et de gérer leur compte. Les administrateurs peuvent ajouter, modifier et supprimer des produits, tandis que les super administrateurs ont des privilèges supplémentaires pour gérer les utilisateurs et les paniers non achetés.

## Fonctionnalités

### Utilisateur

- Inscription et connexion
- Modification du profil
- Ajout de produits au panier
- Passage de commandes
- Consultation de l'historique des commandes

### Administrateur

- Ajout, modification et suppression de produits

### Super Administrateur

- Gestion des utilisateurs inscrits aujourd'hui
- Gestion des paniers non achetés

## Installation

1. **Clonez le dépôt** :
    ```bash
    git clone https://github.com/TerukiIIM/Rendu_Symfony.git
    cd Rendu_Symfony
    ```

2. **Installez les dépendances** :
    ```bash
    composer install
    ```

3. **Configurez les variables d'environnement** :
    Copiez le fichier [.env](http://_vscodecontentref_/1) et renommez-le en [.env.local](http://_vscodecontentref_/2). Modifiez les valeurs des variables d'environnement pour correspondre à votre configuration locale.
    ```bash
    cp .env .env.local
    ```

    Exemple de configuration de base de données dans [.env.local](http://_vscodecontentref_/3)
    - Windows
    ```env
    APP_SECRET=<chaine_aleatoire>
    DATABASE_URL="mysql://root:@127.0.0.1:3306/<nom_de_la_database>?charset=utf8mb4"
    ```
    - Mac
    ```env
    APP_SECRET=<chaine_aleatoire>
    DATABASE_URL="mysql://root:root@127.0.0.1:8889/<nom_de_la_database>?charset=utf8mb4"
    ```

4. **Créez la base de données** :
    ```bash
    php bin/console doctrine:database:create
    ```

5. **Exécutez les migrations** :
    ```bash
    php bin/console doctrine:migrations:migrate
    ```

6. **Lancez le serveur de développement** :
    ```bash
    symfony server:start
    ```

    L'application sera accessible à l'adresse [http://localhost:8000](http://localhost:8000).

## Utilisation

### Page d'accueil

- Affiche la liste des produits.
- Les administrateurs peuvent ajouter de nouveaux produits.

### Fiche produit

- Affiche les détails d'un produit.
- Permet d'ajouter le produit au panier (utilisateur connecté).
- Les administrateurs peuvent modifier ou supprimer le produit.

### Panier

- Affiche les produits ajoutés au panier.
- Permet de passer commande.
- Affiche le montant total du panier.

### Mon compte

- Permet de modifier le profil.
- Affiche l'historique des commandes.

### Super Administration

- Liste les paniers non achetés.
- Affiche les utilisateurs inscrits aujourd'hui.
