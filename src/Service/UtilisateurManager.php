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
        $length=8;
        if ($code === null) {
            do {
                $octetsAleatoires = random_bytes(ceil($length * 6 / 8));
                $code = substr(base64_encode($octetsAleatoires), 0, $length);
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

    public function modifyUser(Utilisateur $utilisateur, ?string $code): void
    {
       $this->generateCode($utilisateur, $code);
    }

}