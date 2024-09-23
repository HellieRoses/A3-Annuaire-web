<?php

namespace App\Service;

use App\Repository\UtilisateurRepository;

class CreateUserHelper implements CreateUserHelperInterface
{

    public function __construct(
        private readonly UtilisateurRepository $utilisateurRepository,
    )
    {
    }

    public function verifyPassword(?string $password): bool
    {
        if (empty($password)) {
            return false;
        }
        /*TODO if (!preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)[a-zA-Z\d\w\W]{8,30}$#", $password)){
            return false;
        }*/
        return true;
    }

    public function verifyLogin(?string $login): bool
    {
        if (empty($login)) {
            return false;
        }
        $countUser = $this->utilisateurRepository->findBy(['login' => $login]);
        if (!empty($countUser)) {
            return false;
        }
        if (strlen($login) >= 180) {
            return false;
        }

        return true;
    }

    public function verifyEmail(?string $email): bool
    {
        if (empty($email)) {
            return false;
        }
        $pattern = "/^[\w\-\+]+(\.[\w\-]+)*@[\w\-]+(\.[\w\-]+)*\.[\w\-]{2,4}$/";

        if (!preg_match($pattern, $email)) {
            return false;
        }
        if (strlen($email) > 250) {
            return false;
        }

        return true;
    }

    public function verifyCode(?string $code): bool
    {
        if (is_null($code)) {
            return false;
        }
        if (empty($code)) {
            return true;
        }
        if (strlen($code) != 8) {
            return false;
        }
        /*TODO
        $pattern = "";
        if (!preg_match($pattern, $code)) {
            return false;
        }
        */
        return true;
    }
}