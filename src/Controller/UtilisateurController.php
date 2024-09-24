<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\InscriptionUtilisateurType;
use App\Form\ProfilUtilisateurType;
use App\Repository\UtilisateurRepository;
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
        private readonly MessageFlashManagerInterface $messageFlashManager,
        private UtilisateurRepository $utilisateurRepository,
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
            return $this->redirectToRoute('connexion');
        }
        $this->messageFlashManager->addFormErrorsAsFlash($form);
        return $this->render('utilisateur/inscription.html.twig',
        ['form'=>$form->createView()]);
    }

    #[Route('/connexion', name:'connexion', methods: ['GET','POST'])]
    public function connexion(AuthenticationUtils $authenticationUtils):Response
    {
        if($this->isGranted('ROLE_USER')){
            return $this->redirectToRoute('listeUtilisateurs');
        }
        $lastLogin = $authenticationUtils->getLastUsername();
        return $this->render('utilisateur/connexion.html.twig',['last_login' => $lastLogin]);
    }

    #[Route('/profil/edition', name:'editionProfil', methods: ['GET', 'POST'])]
    public function editionProfil(Request $request):Response
    {
        if(!$this->isGranted('ROLE_USER')){
            return $this->redirectToRoute('connexion');
        }
        $user = $this->getUser();
        $form= $this->createForm(ProfilUtilisateurType::class, $user,options:[
            'method'=>'POST',
            'action'=>$this->generateUrl('editionProfil')
        ]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $code =  $form->get('code')->getData();
            $this->utilisateurManager->modifyUser($user,$code);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success','Profil modifié');
        }

        $this->messageFlashManager->addFormErrorsAsFlash($form);
        return  $this->render('utilisateur/modification_profil.html.twig',
        ['form' => $form->createView()]);
    }

    #[Route('/', name:'listeUtilisateurs', methods:['GET'])]
    public function listerUtilisateurs(): Response
    {
        if ($this->isGranted('ROLE_ADMIN')){
            $users = $this->utilisateurRepository->findAll();
        }else {
            $users = $this->utilisateurRepository->findBy(["visible" => 1]);
        }
        return $this->render('utilisateur/listeUtilisateur.html.twig',['users'=>$users]);
    }


    #[Route('/profil/utilisateur/{code}', name:'profil', methods: ['GET'])]
    public function profil(string $code, UtilisateurRepository $repository):Response
    {
        $utilisateur = $repository->findOneBy(["code" => $code]);

        return $this->render('utilisateur/profil.html.twig', ['utilisateur' => $utilisateur]);
    }

}
