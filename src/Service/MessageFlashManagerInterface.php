<?php

namespace App\Service;

use Symfony\Component\Form\FormInterface;

interface MessageFlashManagerInterface
{
    public function addFormErrorsAsFlash(FormInterface $form): void;
}