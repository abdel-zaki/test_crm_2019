Documentation d'installation
----------------------------

1)	Installation
- A la racine src/, exécuter la commande :
composer install
- Exécuter upgrade.sql (Création et optimisation de la BD)


2)	Données de testes
Vous avez un utilisateur dans la base de données pour tester :
Login : admin / pwd : admin


3)	Test unitaire
Le contrôleur            /src/app/Controllers/ContactController.php
est testé par la classe  /src/ContactControllerTest.php


Pour lancer le test, exécuter à la racine /src/ :
phpunit ContactControllerTest.php -stderr


NB : 
->	-stderr : Afin d'éviter une erreur déclenchée par la fonction header() de php.
->	Avant d'exécuter la commande, éditer la classe ContactControllerTest.php, puis affecter à la variable $HTTP_HOST la valeur appropriée de l'hôte.
