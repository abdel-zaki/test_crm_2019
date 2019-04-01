<?php

define('ROOT', dirname(__DIR__ . '/..'));
require ROOT . '/app/App.php';
App::load();

//var_dump($_GET['url']);

if (isset($_GET['url'])) {
    $page = $_GET['url'];
} else {
    $page = 'user/login';
}

$page = explode('/', $page);
if (count($page) < 2) {
    die('L\'URL n\'est pas bonne');
}

/**
 * Appel de l'API
 */

if ($page[0] == 'api') {
    if ($page[1] == 'palindrome' || $page[1] == 'email') {
        $api = '\App\Components\Api\Api';
        $api = new $api($page[1]);
    }
}
else {
    /**
     * Appel d'un contrôleur
     */
    $controller = '\App\Controllers\\' . ucfirst($page[0]) . 'Controller';
    $action = $page[1];
    $controller = new $controller();
    if (count($page) == 3) {
        if (!ctype_digit($page[2])) {
            die('Erreur : URL non valide !');
        }
        echo $controller->$action($page[2]);
    }
    else if (count($page) == 2) {
        echo $controller->$action();
    }
    else {
        die('Erreur : URL non valide !');
    }
}
