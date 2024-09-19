<?php

namespace App\EventSubscriber;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
class AuthenticationSubscriber
{

    public function __construct(private RequestStack $requestStack){}

    #[AsEventListener]
    public function loginSuccess(LoginSuccessEvent $event){
        $flashBag = $this->requestStack->getSession()->getFlashBag();
        $flashBag->add("success", "Connexion réussie !");
    }
    #[AsEventListener]
    public function loginFailure(LoginFailureEvent $event){
        $flashBag = $this->requestStack->getSession()->getFlashBag();
        $flashBag->add("error", "Mauvais login ou mot de passe");
    }
    #[AsEventListener]
    public function logout(LogoutEvent $event){
        $flashBag = $this->requestStack->getSession()->getFlashBag();
        $flashBag->add("success", "Déconnexion réussie!");
    }
}