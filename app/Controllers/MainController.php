<?php

namespace App\Controllers;

use App;
use App\Components\Auth\Auth;
use \Twig_Loader_Filesystem;
use \Twig_Environment;
use \App\Models\ContactModel as Contact;
use Exception;

class MainController
{
    /** @var Twig_Environment */
    protected $twig;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->loader = new \Twig_Loader_Filesystem(ROOT . '/app/Views');
        $this->twig   = new \Twig_Environment($this->loader);
        $this->auth   = new Auth(App::getInstance()->getDatabase());
        $this->twig->addGlobal('session', $_SESSION);
        $this->Contact = new Contact(App::getInstance()->getDatabase());
    }

    /**
     * Méthode de chargement de model
     * @param $model
     */
    protected function loadModel($model)
    {
        $this->$model = App::getInstance()->getModel($model);
    }

    /**
     * @param $methode
     * @param array $datas
     * @return mixed
     */
    public function apiClient($methode, $datas = [])
    {
        $api = $_SERVER['HTTP_HOST'].'/api/'.$methode;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $api);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $datas);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $return = curl_exec($curl);
        curl_close($curl);
        return json_decode($return);
    }
}