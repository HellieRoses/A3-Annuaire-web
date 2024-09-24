<?php

namespace App\EventSubscriber;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Twig\Environment;

class MaintenanceSubscriber
{

    public function __construct(
        #[Autowire("%maintenance_mode%")] private bool $maintenanceMode,
        private readonly Environment $twig,
    )
    {
    }


    #[AsEventListener]
    public function onMaintenance(RequestEvent $event){
        if ($this->maintenanceMode) {
            $event->setResponse(new Response(
                $this->twig->render('maintenance.html.twig', [])
            ));
        }
        $event->stopPropagation();
    }
}