<?php

namespace App\Controllers;

use App\Controllers\ControllerInterface;
use Exception;

class AddressController extends MainController implements ControllerInterface
{
    /**
     * AddressController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        if (!isset($_SESSION['auth'])) {
            header('Location: /user/login');
        }
        $this->loadModel('Addresse');
        $this->loadModel('Contact');
    }

    /**
     * Affichage de la liste des adresses d'un Utilisateur
     * @param int|null $idContact
     * @return string
     */
    public function index(int $idContact = null): string
    {
        $contact = [];
        try {
            $contact = $this->Contact->findById($idContact);
            if (!$contact) {
                throw new Exception('Erreur : ID contact introuvable !');
            }
            $address = $this->Addresse->getByContact($idContact);
            return $this->twig->render('addresslist.html.twig', [
                'addresses' => $address,
                'idContact' => $idContact,
                'contact'   => $contact
            ]);
        }
        catch (Exception $e) {
            return $this->twig->render('addresslist.html.twig', [
                'addresses' => [],
                'idContact' => $idContact,
                'contact'   => $contact,
                'message' => [$e->getMessage()], 'error' => true
            ]);
        }
    }

    /**
     * Ajout d'adresse pour un contact
     * @param int|null $id
     * @return string
     */
    public function add(int $id = null): string
    {
        $error = false;
        $data = [];
        if (!empty($_POST)) {
            try {
                $data = [
                    'number'     => $_POST['number'],
                    'street'     => $_POST['street'],
                    'postalCode' => $_POST['postalCode'],
                    'city'       => $_POST['city'],
                    'country'    => $_POST['country'],
                    'idContact'  => $_POST['idContact']
                ];
                // Nettoyage
                $response = $this->sanitize($_POST);
                if ($response["response"]) {
                    $idContact = $response['idContact'];
                    $result = $this->Addresse->create([
                        'number'     => $response['number'],
                        'street'     => $response['street'],
                        'postalCode' => $response['postalCode'],
                        'city'       => $response['city'],
                        'country'    => $response['country'],
                        'idContact'  => $response['idContact']
                    ]);
                    if ($result) {
                        header("Location: /address/index/$idContact");
                    } else {
                        $error = true;
                    }
                } else {
                    $error = true;
                }
            }
            catch (Exception $e) {
                return $this->twig->render('addressadd.html.twig',
                    ["idContact" => $id, 'data' => $data, 'message' => [$e->getMessage()], 'error' => $error]);
            }
        }
        return $this->twig->render('addressadd.html.twig',
            ["idContact" => $id, 'data' => $data, 'error' => $error]);
    }

    /**
     * Modification d'une adresse d'un contact
     * @param int $id
     * @return string
     */
    public function edit(int $id): string
    {
        $error = false;
        $data = [];
        try {
            $data = $this->Addresse->getById($id);
            if (!empty($_POST)) {
                $data = [
                    'number'     => $_POST['number'],
                    'street'     => $_POST['street'],
                    'postalCode' => $_POST['postalCode'],
                    'city'       => $_POST['city'],
                    'country'    => $_POST['country'],
                    'idContact'  => $_POST['idContact']
                ];
                $response = $this->sanitize($_POST);
                if ($response["response"]) {
                    $addresse = $this->Addresse->findById($id);
                    $result = $this->Addresse->update($id,
                        [
                            'number'     => $response['number'],
                            'street'     => $response['street'],
                            'postalCode' => $response['postalCode'],
                            'city'       => $response['city'],
                            'country'    => $response['country']
                        ]);
                    if ($result) {
                        header("Location: /address/index/$addresse->idContact");
                    } else {
                        $error = true;
                    }
                } else {
                    $error = true;
                }
            }
        }
        catch (Exception $e) {
            return $this->twig->render('addressadd.html.twig',
                ["idContact" => $id, 'data' => $data, 'message' => [$e->getMessage()], 'error' => $error]);
        }
        return $this->twig->render('addressadd.html.twig',
            [
                'data'      => $data,
                "idContact" => $data->idContact
            ]);
    }

    /**
     * Suppression d'une adresse d'un contact
     * @param int $id
     */
    public function delete(int $id)
    {
        try {
            $addresse = $this->Addresse->getById($id);
            $idAddresse = get_object_vars($addresse)['idContact'];
            $result = $this->Addresse->delete($id);
            if ($result) {
                header('Location: /address/index/'.$idAddresse);
            }
        }
        catch (Exception $e) {
            die("Erreur : ".$e->getMessage());
        }
    }

    /**
     * Vérifie les contrainte d'enregistrement
     *
     * @param array $data
     *
     * @return array
     */
    public function sanitize(Array $data = []): array
    {
        $number     = trim($data['number']);
        $street     = trim(strtoupper($data['street']));
        $postalCode = trim($data['postalCode']);
        $city       = trim(strtoupper($data['city']));
        $country    = trim(strtoupper($data['country']));
        $idContact  = intval(trim($data['idContact']));

        if (!$number || !is_numeric($number)) {
            throw new Exception('Le Numéro est obligatoire et doit être numérique');
        }

        if (!$street) {
            throw new Exception('La rue est obligatoire');
        }

        if (!$postalCode || !is_numeric($postalCode)) {
            throw new Exception('Le code postal est obligatoire et doit être numérique');
        }

        if (!$city) {
            throw new Exception('La ville est obligatoire');
        }

        if (!$country) {
            throw new Exception('Le pays est obligatoire');
        }

        if (!$idContact || !is_numeric($idContact)) {
            throw new Exception('L\'id contact est obligatoire et doit être numérique');
        }

        if ($number && $street && $postalCode && $city && $country && $idContact) {
            return [
                'response'   => true,
                'number'     => intval($number),
                'street'     => $street,
                'postalCode' => intval($postalCode),
                'city'       => $city,
                'country'    => $country,
                'idContact'  => intval($idContact)
            ];
        } else {
            return ['response' => false];
        }
    }

}