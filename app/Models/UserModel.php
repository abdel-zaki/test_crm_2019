<?php

namespace App\Models;

use App\Models\AbstractModel;

class UserModel extends AbstractModel
{
    /** @var string  */
    protected $table = "users";

    /**
     * @param $login
     * @return array|bool|false|mixed|\PDOStatement
     */
    public function findIdByLogin($login)
    {
        return $this->query("SELECT * FROM {$this->model} WHERE login = ?", [$login], true);
    }

}