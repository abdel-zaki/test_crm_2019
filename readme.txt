Documentation d'installation
----------------------------

1)	Installation
- A la racine exécuter la commande :
composer install
- Exécuter upgrade.sql (Création et optimisation de la BD)


2)	Données de testes
Vous avez un utilisateur dans la base de données pour tester :
Login : admin / pwd : admin


3)	Test unitaire
Le contrôleur           /app/Controllers/ContactController.php
à tester par la classe  /ContactControllerTest.php


Pour lancer le test, exécuter à la racine :
> phpunit ContactControllerTest.php -stderr


NB : 
->	-stderr : Afin d'éviter une erreur déclenchée par la fonction header() de php.
->	Avant d'exécuter la commande, éditer la classe ContactControllerTest.php, puis affecter à la variable $HTTP_HOST la valeur appropriée de l'hôte.
