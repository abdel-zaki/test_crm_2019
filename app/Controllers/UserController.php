<?php

namespace App\Controllers;

use App\Controllers\MainController;
use App;
use Exception;

class UserController extends MainController
{
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        if (isset($_SESSION['auth'])) {
            header('Location: /contact/index');
        }
    }

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @return string
     */
    public function login()
    {
        try {
            $errors = false;
            if (!empty($_POST)) {
                if ($this->auth->login($_POST['login'], $_POST['password'])) {
                    header('Location: /contact/index');
                } else {
                    $errors = true;
                }
            }
            return $this->twig->render('login.html.twig', ['errors' => $errors]);
        }
        catch (Exception $e) {
            return $this->twig->render('login.html.twig', ['errors' => true]);
        }
    }

    /**
     * Déconnecter un utilisateur
     */
    public function logout()
    {
        unset($_SESSION['auth']);
        header('Location: /user/login');
    }
}