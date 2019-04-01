<?php

namespace App\Models;

use App\Models\AbstractModel;
use App\Models\ContactModel;
use Exception;

class AddresseModel extends AbstractModel
{
    /** @var string  */
    protected $table = "addresses";

    /**
     * Méthode pour résupérer la liste toutes les adresses
     * @return array|bool|mixed|\PDOStatement
     */
    public function getAll()
    {
        $result = $this->query("SELECT * FROM $this->table");
        if (!$result) {
            throw new Exception('Erreur : Aucune adresse trouvée !');
        }
        return $result;
    }

    /**
     * Méthode de récupération des adresses d'utilisateur
     * @param $idContact
     *
     * @return array|bool|mixed|\PDOStatement
     */
    public function getByContact($idContact)
    {
        $result = $this->query("SELECT * FROM $this->table WHERE idContact = $idContact");
        if (!$result) {
            throw new Exception('Aucune adresse trouvée pour ce contact !');
        }
        return $result;
    }

    /**
     * Méthode de récupération d'une adresse à partir de don Id
     * @param $idAdresse
     *
     * @return array|bool|mixed|\PDOStatement
     */
    public function getById($idAdresse)
    {
        $result = $this->findById($idAdresse);
        if (!$result) {
            throw new Exception('Cette adresse n\'existe pas !');
        }
        return $result;
    }

}