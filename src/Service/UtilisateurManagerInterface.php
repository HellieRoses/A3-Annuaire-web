<?php

namespace App\Service;

use App\Entity\Utilisateur;

interface UtilisateurManagerInterface
{
    public function createUser(Utilisateur $utilisateur,string $password,?string $code);
    public function verifyPassword(?string $password): bool;

    public function verifyLogin(?string $login): bool;
}