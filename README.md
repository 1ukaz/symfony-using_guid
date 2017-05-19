# Symfony2 Project using the GUID NOT as a Primary Key

I created this tiny Project because I needed to implement `MySQL GUID()` Identifier NOT as Primary Key

Use this application to quickly review Lucas Guegnolle's (Limited) Coding Skills.

I know that certain things were unnecesary (The OrderRepository for instance).
(But for the the testing point of view I wanted to show its meanning).

This application was built with Composer. This makes setting it up quick and easy.

It is a standard Symfony2 v2.8 default instalation via the command:
`composer create-project symfony/framework-standard-edition symfony-using_guid "2.8.*"`

It is a default Symfony2 version 2.8 default instalation via `composer`

I simply added a `composer require symfony/console:2.8` because Commands Console is not included :-(
(Be sure to stick to version 2.8 since that's the Symfony2 version implemented)

Right now and because of testing purposes only. You won't need to run Composer (You're welcome!)

## Install the Application

* Clone this Repository: `git clone https://github.com/1ukaz/symfony-using_guid.git`

* Get inside the folder where you cloned into: Defaults to `symfony-using_guid`

* Add your parameters on `app/config/parameters.yml` based from `app/config/parameters.yml.dist` file.

## Run it!

* Execute `app/console server:start` to run PHP Built in Server in the background within root Symfony dir.
  (In windows you will have to run `app/console server:run` due to a PHP Library not available for Windows)

* In order to create the Database and the tables run:
    `app/console doctrine:database:create`
    `app/console doctrine:schema:create`

* If you like you can run `app/console app:database:create-dummy-data` to get a Dummy Dataset (From me to you).

* Go ahead with Browser and hit: `http://127.0.0.1:8000`

* Unit Tests require PHPUnit v4.8.35+ or v5.4+ (Just go for the PHP 7.*.*).

* That's it! Now go and hire the poor devil!
