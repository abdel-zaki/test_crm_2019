<?php

namespace App\Components\Api;

use App\Components\Api\ApiService;
use App\Components\Api\Palindrome;

class Api extends ApiService
{
    /**
     * Controller constructor.
     */
    public function __construct($methode)
    {
        $this->methode = $methode;
        parent::__construct();
    }

    /**
     * Palindrome
     */
    public function palindrome()
    {
        if ($this->getRequestMethod() != "POST") {
            $this->response('', 406);
        }

        $name = $this->request['name'];
        $palindrome = new palindrome();
        $palindrome->setName($name);

        if ($name) {
            if ($palindrome->is_valid()) {
                $this->response($this->json([
                    "response" => true,
                    "message"  => "- Le nom du contact ne peut pas être un palindrome"
                ]), 200);
            } else {
                $this->response($this->json([
                    "response" => false,
                    "message"  => "- Le nom n'est pas un palindrome"
                ]), 200);
            }
        }
    }

    /**
     * Vérification du format de l'email
     */
    public function email()
    {
        if ($this->getRequestMethod() != "POST") {
            $this->response('', 406);
        }
        $email = $this->request['email'];
        if ($email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->response($this->json([
                    "response" => true,
                    "message"  => "- L'email est au bon format"
                ]), 200);
            } else {
                $this->response($this->json([
                    "response" => false,
                    "message"  => "- Le format de l'email n'est pas correct"
                ]), 200);
            }
        }
    }

    /**
     * Encodage des données en json
     *
     * @param $data
     *
     * @return string
     */
    private function json($data)
    {
        if (is_array($data)) {
            return json_encode($data);
        }
    }
}
