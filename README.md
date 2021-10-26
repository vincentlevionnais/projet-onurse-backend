# Installation 

Le projet utilise la version Website Skeleton de Symfony
  
Après avoir cloné le projet :
  
- Faire la commande pour l'installation des dépendances  
 ` composer install `
 
- Générer les clés JWT sur l'environnement  
 `php bin/console lexik:jwt:generate-keypair`

- Paramétrer la variable `.env.local` pour la configuration  
 `"DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"`

- Création de la Database  
 `bin/console doctrine:database:create`

- Transmission des migrations à la Database  
 `bin/console doctrine:migrations:migrate`
