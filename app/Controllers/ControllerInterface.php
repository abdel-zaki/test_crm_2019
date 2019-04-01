<?php

namespace App\Controllers;

interface ControllerInterface
{
    /**
     * Methode pour page d'accueil
     * @param int|null $id
     * @return string
     */
    public function index(int $id = null): string;

    /**
     * Methode pour page de creation
     * @param int|null $id
     * @return string
     */
    public function add(int $id = null): string;

    /**
     * Methode pour page de modification
     * @param int $id
     * @return string
     */
    public function edit(int $id): string;

    /**
     * Methode pour page de suppression
     * @param int $id
     */
    public function delete(int $id);

    /**
     * @param array $data
     *
     * @return array
     */
    public function sanitize(array $data = []): array;

}