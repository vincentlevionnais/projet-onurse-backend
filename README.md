# Installation de Symfony

- commande pour installation du website skeleton
 ` composer create-project symfony/website-skeleton `
  
- Installation du pack pour visualisation dans le navigateur
 `composer require apache-pack`

- Installation du pack barre de débug Symfony
  `composer require profiler`
  `composer require debug-bundle`

- Paramétrage de la variable `.env.local` pour la configuration
 `"DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"`

# Création de la Database
 `bin/console doctrine:database:create`

 - Transmission des migrations à la Database
`bin/console doctrine:migration:migrate` 

- Chargement des fixtures si besoin
 `bin/console doctrine:fixtures:load -n`


