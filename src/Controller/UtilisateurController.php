<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\InscriptionUtilisateurType;
use App\Service\MessageFlashManager;
use App\Service\MessageFlashManagerInterface;
use App\Service\UtilisateurManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UtilisateurController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UtilisateurManagerInterface $utilisateurManager,
        private readonly MessageFlashManagerInterface $messageFlashManager
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
            $password = $form->get('plainPassword')->getData();
            $code =  $form->get('code')->getData();
            $this->utilisateurManager->createUser($user,$password,$code);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success','Vous vous êtes inscrit');
        }
        $this->messageFlashManager->addFormErrorsAsFlash($form);
        return $this->render('utilisateur/inscription.html.twig',
        ['form'=>$form->createView()]);
    }

    #[Route('/connexion', name:'connexion', methods: ['GET','POST'])]
    public function connexion(AuthenticationUtils $authenticationUtils):Response
    {
        if($this->isGranted('ROLE_USER')){
            return $this->redirectToRoute('test');
        }
        $lastLogin = $authenticationUtils->getLastUsername();
        return $this->render('utilisateur/connexion.html.twig',['last_login' => $lastLogin]);
    }

    #[Route('/test', name:'test', methods: ['GET'])]
    public function test():Response
    {
        return $this->render('test.html.twig');
    }
}
