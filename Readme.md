# Modular design of a Contract Management System

## Installation
1. Clone the repo on your machine
2. In the console (terminal) navigate to the api folder in the app root `cd .../cms-modular/api`
3. Create an environment file (.env) by copying the contents of .env.example. Make the changes to point the application to your database.
4. You will need a stripe account to run the application, setup one and get the developers keys or request for one from me. Once you have the key, add it to your .env file as STRIPE_KEY and STRIPE_SECRET. Do not forget to add a product ID in there too.
5. Run `composer install` to set up the dependencies.
6. Run `php artisan setup` to run all migrations and seeders as well as create Personal Access Tokens for auth. This also pulls the product data and pricing tiers from Stripe.
7. Run `php artisan serve` to serve up the application.