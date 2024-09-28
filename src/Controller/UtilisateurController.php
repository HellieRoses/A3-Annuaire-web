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
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Serializer\SerializerInterface;

class UtilisateurController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface       $entityManager,
        private readonly UtilisateurManagerInterface  $utilisateurManager,
        private readonly MessageFlashManagerInterface $messageFlashManager,
        private UtilisateurRepository                 $utilisateurRepository,
    )
    {
    }

    #[Route('/inscription', name: 'inscription', methods: ['POST'])]
    public function inscription(Request $request):Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(InscriptionUtilisateurType::class, $user, options: [
            'method' => 'POST',
            'action' => $this->generateUrl('inscription')
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('plainPassword')->getData();
            $code = $form->get('code')->getData();
            $this->utilisateurManager->createUser($user, $password, $code);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success','Vous vous êtes inscrit');
            return $this->redirectToRoute('register',['form' => null,'login'=>null,"page_name" => null]);
        }
        $this->messageFlashManager->addFormErrorsAsFlash($form);
        return $this->redirectToRoute('register');
    }

    #[Route('/connexion', name:'connexion', methods: ['POST'])]
    public function connexion(AuthenticationUtils $authenticationUtils):Response
    {
        if ($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('listeUtilisateurs');
        }
        return $this->redirectToRoute('register');
    }

    #[Route('/register', name:"register", methods: ['GET'])]
    public function register(Request $request,AuthenticationUtils $authenticationUtils):Response
    {
        if($this->isGranted('ROLE_USER')){
            return $this->redirectToRoute('listeUtilisateurs');
        }
        $user = new Utilisateur();
        $form= $this->createForm(InscriptionUtilisateurType::class, $user,options:[
            'method'=>'POST',
            'action'=>$this->generateUrl('inscription')
        ]);
        $form->handleRequest($request);
        $lastLogin = $authenticationUtils->getLastUsername();
        return $this->render('utilisateur/register.html.twig', ['form' => $form, 'last_login' => $lastLogin, "page_name" => null]);
    }

    #[Route('/profil/edition', name: 'editionProfil', methods: ['GET', 'POST'])]
    public function editionProfil(Request $request): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('connexion');
        }
        $user = $this->getUser();
        $form = $this->createForm(ProfilUtilisateurType::class, $user, options: [
            'method' => 'POST',
            'action' => $this->generateUrl('editionProfil')
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $code = $form->get('code')->getData();
            $this->utilisateurManager->modifyUser($user, $code);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', 'Profil modifié');
        }

        $this->messageFlashManager->addFormErrorsAsFlash($form);
        return  $this->render('utilisateur/modification_profil.html.twig',
        ['form' => $form->createView(), "page_name" => null]);
    }

    #[Route('/', name: 'listeUtilisateurs', methods: ['GET'])]
    public function listerUtilisateurs(): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $users = $this->utilisateurRepository->findAll();
        } else {
            $users = $this->utilisateurRepository->findBy(["visible" => 1]);
        }
        return $this->render('utilisateur/listeUtilisateur.html.twig',['users'=>$users, 'page_name' => 'userList']);
    }

    #[Route('/profil/utilisateur/{code}', name:'profil', methods: ['GET'])]
    public function profil(?Utilisateur $utilisateur):Response
    {
        $page_name = null;
        if ($utilisateur != null) {
            $page_name = null;
            if($code=== $this->getUser()->getCode() ){
                $page_name="profilUser";
            }
            return $this->render('utilisateur/profil.html.twig', ['utilisateur' => $utilisateur,"page_name" => $page_name]);
        }
        else {
            $this->addFlash("error","L'utilisateur n'existe pas");
            return $this->redirectToRoute("listeUtilisateurs");
        }
    }


    #[Route('/profil/delete/{code}',name: 'supprimerUtilisateur', methods: ['POST'])]
    public function suppressionProfil(?Utilisateur $utilisateur):Response
    {
        if (is_null($utilisateur)) {
            $this->addFlash("error","L'utilisateur n'existe pas");
            return $this->redirectToRoute('listeUtilisateurs');
        }
        if ($this->getUser() == $utilisateur) {
            $session = new Session();
            $session->invalidate();
            $this->redirectToRoute("_logout_main");
        }
        dump($this->getUser());
        $this->denyAccessUnlessGranted('UTILISATEUR_DELETE', $utilisateur);
        $this->entityManager->remove($utilisateur);
        $this->entityManager->flush();
        $this->addFlash("success","L'utilisateur a bien été supprimé");
        return $this->redirectToRoute("listeUtilisateurs");
    }


    #[Route('/profil/utilisateur/{code}/json', name:'profilFormatJSON', methods: ['GET'])]
    public function profilFormatJSON(?Utilisateur $utilisateur, SerializerInterface $serializer):Response
    {
        if ($utilisateur !== null) {
            $utilisateurArray = $serializer->normalize($utilisateur);
            unset($utilisateurArray["password"]);
            $jsonUtilisateur = json_encode($utilisateurArray);
            return new JsonResponse($jsonUtilisateur, 200, ['Content-Type' => 'application/json']);
        }
        else {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }
    }
}
