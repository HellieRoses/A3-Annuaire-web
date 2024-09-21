<?php

namespace App\Service;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class UtilisateurManager implements UtilisateurManagerInterface
{

    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly UtilisateurRepository $utilisateurRepository,
    )
    {
    }

    private function hashPassword(Utilisateur $utilisateur, string $password): void
    {
        $passWordHache = $this->userPasswordHasher->hashPassword($utilisateur, $password);
        $utilisateur->setPassword($passWordHache);
    }

    private function generateCode(Utilisateur $utilisateur, ?string $code): void
    {
        if ($code === null) {
            do {
                $octetsAleatoires = random_bytes(ceil(8 * 6 / 8));
                $code = substr(base64_encode($octetsAleatoires), 0, 8);
            } while ($this->utilisateurRepository->findOneBy(['code' => $code]) !== null);
        }
        $utilisateur->setCode($code);
    }

    public function createUser(Utilisateur $utilisateur, string $password, ?string $code): void
    {
        //hasher le mot de passe lors de l'inscription
        $this->hashPassword($utilisateur, $password);
        //si aucun code n'est renseigné, généré une chaine de caractére aléatoire
        $this->generateCode($utilisateur, $code);
    }

    public function verifyPassword(?string $password): bool
    {
        if (empty($password)) {
            return false;
        }
        /*if (!preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)[a-zA-Z\d\w\W]{8,30}$#", $password)){
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
        return true;
    }

}