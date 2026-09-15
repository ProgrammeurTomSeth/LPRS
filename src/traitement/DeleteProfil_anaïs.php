<?php

class DeleteProfil_anaïs
{
    private $pdo;

    public function __construct() {
        $this->pdo = Bdd::getConnection();
    }

}