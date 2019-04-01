<?php

namespace App\Models;

use App;
use App\Database;
use App\Models\AbstractModel;
use Exception;

class ContactModel extends AbstractModel
{
    /** @var string  */
    protected $table = "contacts";

    /**
     * ContactModel constructor.
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        parent::__construct($database);
    }

    /**
     * Méthode de récupération des contacts d'un utilisateur
     * @param $idUser
     *
     * @return array|bool|mixed|\PDOStatement
     */
    public function getContactByUser($idUser)
    {
        $result = $this->query("SELECT * FROM {$this->table} WHERE userId = $idUser");
        return $result;
    }

    /**
     * Méthode de récupération d'un contact par son id
     * @param $idContact
     *
     * @return array|bool|mixed|\PDOStatement
     */
    public function getContactById($idContact)
    {
        $result = $this->query("SELECT * FROM {$this->table} WHERE id = $idContact");
        if (count($result) == 0) {
            throw new Exception('Contact introuvable !');
        }
        return $result;
    }

}
