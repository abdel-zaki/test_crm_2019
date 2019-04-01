<?php

namespace App\Components\Api;

class Palindrome
{
    /** @var $name */
    private $name;

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param $name
     */
    public function is_valid()
    {
        $name = strtolower($this->name);
        if (strrev($name) == $name) {
            return true;
        }
        else {
            return false;
        }
    }
}