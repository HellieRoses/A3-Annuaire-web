<?php

namespace App\Service;

interface CreateUserHelperInterface
{
    public function verifyPassword(?string $password): bool;

    public function verifyLogin(?string $login): bool;

    public function verifyEmail(?string $email): bool;

    public function verifyCode(?string $code,bool $generate): bool;
}