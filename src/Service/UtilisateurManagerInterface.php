<?php

namespace App\Service;

use App\Entity\Utilisateur;

interface UtilisateurManagerInterface
{
    public function createUser(Utilisateur $utilisateur,string $password,?string $code);
}