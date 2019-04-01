<?php

namespace App\Controllers;

use App\Controllers\MainController;
use App\Controllers\ControllerInterface;
use InvalidArgumentException;
use Exception;

class ContactController extends MainController implements ControllerInterface
{
    /** @var int $userId */
    protected $userId;

    /**
     * ContactController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        if (!isset($_SESSION['auth'])) {
            header('Location: /user/login');
        }
        $this->userId = $_SESSION['auth']['id'];
    }

    /**
     * Affichage de la liste des contacts de l'utilisateur connecté
     * @param int|null $id
     * @return string
     */
    public function index(int $id = null): string
    {
        if (!$id) {
            if (!empty($this->userId)) {
                $contacts = $this->Contact->getContactByUser($this->userId);
            }
            return $this->twig->render('index.html.twig', ['contacts' => $contacts]);
        }
        else {
            die('Erreur : URL non valide !');
        }
    }

    /**
     * Ajout d'un contact
     * @param int|null $id
     * @return string
     */
    public function add(int $id = null): string
    {
        $message = '';
        $data = [];
        if (!empty($_POST)) {
            try {
                $data = [
                    'nom'    => $_POST['nom'],
                    'prenom' => $_POST['prenom'],
                    'email'  => $_POST['email'],
                    'userId' => $this->userId
                ];
                $response = $this->sanitize($_POST);
                if ($response["response"]) {
                    $result = $this->Contact->create([
                        'nom'    => $response['nom'],
                        'prenom' => $response['prenom'],
                        'email'  => $response['email'],
                        'userId' => $this->userId
                    ]);
                    if ($result) {
                        header('Location: /contact/index');
                    }
                } else {
                    $message = $response["message"];
                }
            }
            catch (Exception $e) {
                return $this->twig->render('add.html.twig', ['data' => $data, 'message' => [$e->getMessage()]]);
            }
        }
        return $this->twig->render('add.html.twig', ['data' => $data, 'message' => $message]);
    }

    /**
     * Modification d'un contact
     * @param int $id
     * @return string
     */
    public function edit(int $id): string
    {
        $message = '';
        $data = [];
        try {
            $contact = $this->Contact->getContactById($id);
            $data = $contact[0];
            if (!empty($_POST)) {
                $data = [
                    'id'    => $id,
                    'nom'    => $_POST['nom'],
                    'prenom' => $_POST['prenom'],
                    'email'  => $_POST['email']
                ];
                $response = $this->sanitize($_POST);
                if ($response["response"]) {
                    $result = $this->Contact->update($id, [
                        'nom'    => $response['nom'],
                        'prenom' => $response['prenom'],
                        'email'  => $response['email'],
                    ]);
                    if ($result) {
                        header('Location: /contact/index');
                    }
                } else {
                    $message = $response["message"];
                }
            }
        }
        catch (Exception $e) {
            return $this->twig->render('add.html.twig', ['data' => $data, 'message' => [$e->getMessage()]]);
        }
        return $this->twig->render('add.html.twig', ['data' => $data, 'message' => $message]);
    }

    /**
     * Suppression d'un contact
     * @param int $id
     */
    public function delete(int $id)
    {
        $result = $this->Contact->delete($id);
        if ($result) {
            header('Location: /contact/index');
        }
    }

    /**
     * @param array $data
     * @return array
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function sanitize(array $data = []): array
    {
        $nom    = trim(ucfirst(strtolower($data['nom'])));
        $prenom = trim(ucfirst(strtolower($data['prenom'])));
        $email  = trim(strtolower($data['email']));

        if (empty($nom)) {
            throw new Exception('Le nom est obligatoire');
        }

        if (empty($prenom)) {
            throw new Exception('Le prenom est obligatoire');
        }

        if (empty($email)) {
            throw new Exception('Le email est obligatoire');
        }

        $isPalindrome = $this->apiClient('palindrome', ['name' => $nom]);
        $isEmail = $this->apiClient('email', ['email' => $email]);
        if ((!$isPalindrome->response) && $isEmail->response && $prenom) {
            return [
                'response' => true,
                'nom'      => $nom,
                'prenom'   => $prenom,
                'email'    => $email
            ];
        }
        $message = [];
        if ($isPalindrome->response) {
            $message [] = $isPalindrome->message;
        }
        if (!$isEmail->response) {
            $message [] = $isEmail->message;
        }
        return ['response' => false, "message" => $message];
    }

}