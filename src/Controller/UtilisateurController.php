<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\InscriptionUtilisateurType;
use App\Service\UtilisateurManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UtilisateurController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UtilisateurManagerInterface $utilisateurManager
    )
    {
    }

    #[Route('/inscription', name: 'inscription', methods: ['GET', 'POST'])]
    public function inscription(Request $request):Response
    {
        $user = new Utilisateur();
        $form= $this->createForm(InscriptionUtilisateurType::class, $user,options:[
            'method'=>'POST',
            'action'=>$this->generateUrl('inscription')
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dump($user);
            $password = $form->get('plainPassword')->getData();
            $code =  $form->get('code')->getData();
            $this->utilisateurManager->createUser($user,$password,$code);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success','Vous vous êtes inscrit');
        }
        return $this->render('utilisateur/inscription.html.twig',
        ['form'=>$form->createView()]);
    }
}
