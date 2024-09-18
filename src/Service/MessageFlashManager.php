<?php

namespace App\Service;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class MessageFlashManager implements MessageFlashManagerInterface
{
    public function __construct(private readonly RequestStack $requestStack){}

    public function addFormErrorsAsFlash(FormInterface $form) : void
    {
        $errors = $form->getErrors(true);
        $flashbag= $this->requestStack->getSession()->getFlashBag();
        foreach ($errors as $error) {
            $flashbag->add('error', $error->getMessage());
        }
    }
}