# Installation 

- Le projet utilise la version Website skeleton de Symfony
  
- Après avoir cloné le projet
  
- Faire la commande pour l'installation des dépendances
 ` composer install `

- Paramétrer la variable `.env.local` pour la configuration
 `"DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"`

# Création de la Database
 `bin/console doctrine:database:create`

 - Transmission des migrations à la Database
`bin/console doctrine:migrations:migrate` 


# Chargement de fixtures si besoin
 `composer require orm-fixtures `
 `bin/console doctrine:fixtures:load -n`


# Sécurisation session utilisateur avec JWT
 `composer require lexik/jwt-authentication-bundle`
 -générer les clés JWT sur l'environnement :
 `php bin/console lexik:jwt:generate-keypair`

# Gestion des CORS
`composer req cors`

# Mise en place des tests
`composer require --dev phpunit/phpunit symfony/test-pack`

# Reset password
`composer require symfonycasts/reset-password-bundle`






