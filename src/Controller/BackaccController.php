<?php

namespace App\Controller;

use App\Entity\Reclamations;
use App\Entity\Utilisateur;
use App\Entity\Produits;
use App\Form\AddUtilisateurType;
use App\Form\ReclamationsType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use  Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
class BackaccController extends AbstractController
{

    private $em ;
    private $up ;
    public function __construct(UtilisateurRepository $up,EntityManagerInterface $em)
    {
        $this->up = $up;
        $this->em = $em ;
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/blog", name="bloggg")
     */
    public function utilisateuraffback (Request $request,UserPasswordEncoderInterface $encoder): Response
    {
        $session =  $request->getSession()->get('email');
        $produit=$this->getDoctrine()->getManager()->getRepository(Produits::class)->findAll();
        $us = $this->up->findOneBy(array('email'=>$session),array());
        $uss = $this->getUser();

        $user = new Utilisateur();
        $users = $this->up->findByRole('ROLE_FOUR');
        $form= $this->createForm(AddUtilisateurType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $hash = $encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($hash);
            $this->em->persist($user);
            $this->em->flush();
            return $this->redirect("/blog");
        }
        else {
            return $this->render('back/base.html.twig',
                ['form'=>$form->Createview(),'users'=>$users,'us'=>$us,'uss'=>$uss,'produit'=>$produit]);
        }

    }

    /**
     *@IsGranted("ROLE_ADMIN")
     * @Route("/listutili", name="bloglistutili")
     */
    public function listutili(): Response
    {
        $user=$this->getUser();
        $users = $this->up->findByRole('ROLE_ADMIN');
        return $this->render('back/list_utilisateurs.html.twig', [
            'controller_name' => 'BackaccController', 'us'=>$user,"users"=>$users
        ]);
    }


    /**
     * @Route("/listadmin", name="bloglistadmins")
     */
    public function listadmin(): Response
    {
        $user=$this->getUser();
        $users = $this->up->findByRole('ROLE_ADMIN');
        return $this->render('back/listadmins.html.twig', [
            'controller_name' => 'BackaccController', 'us'=>$user,"users"=>$users
        ]);
    }


    /**
     * @Route("/produitfour", name="produitfourr")
     */
    public function produitfour(): Response
    {
        $user=$this->getUser();
        $en=$this->getDoctrine()->getManager()->getRepository(Produits::class)->findAll();
        $users = $this->up->findByRole('ROLE_ADMIN');
        return $this->render('back/produitfour.html.twig', [
            'controller_name' => 'BackaccController', 'uss'=>$user,"users"=>$users,'prod'=>$en
        ]);
    }


    /**
     * @Route ("/fournisseurc/{id}",name="edit_four")
     */
    public function Edit_fournisseur($id,Request $request,UserPasswordEncoderInterface $encoder)
    {

        $uss = $this->getUser();
        $user = $this->up->findOneBy(array('id'=>$id));
        $form= $this->createForm(AddUtilisateurType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $hash = $encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($hash);
            $this->em->persist($user);
            $this->em->flush();
            return $this->redirect("/edit_four");
        }
        return $this->render('back/utilisateurfournisseurs.html.twig',
            ['form'=>$form->Createview(),'uss'=>$uss,'useer'=>$user]);
    }




    /**
     * @Route("/map", name="blogmap")
     */
    public function map(): Response
    {
        $user=$this->getUser();
        return $this->render('back/maps.html.twig', [
            'controller_name' => 'BackaccController','us'=>$user
        ]);
    }


    /**
     * @Route("/fournisseurs", name="blogfournisseur")
     */
    public function fournisseur(): Response
    {
        $user=$this->getUser();

        return $this->render('back/Fournisseurs.html.twig', [
            'controller_name' => 'BackaccController','us'=>$user
        ]);
    }



    /**
     * @Route("/fournisseur", name="blogg")
     */
    public function fournisseurC (Request $request,UserPasswordEncoderInterface $encoder): Response
    {

$us = $this->getUser();
        $user = new Utilisateur();
        $users = $this->up->findAll();
        $userss = $this->up->findByRole('ROLE_FOUR');
        $form= $this->createForm(AddUtilisateurType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $hash = $encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($hash);
            $this->em->persist($user);
            $this->em->flush();
            return $this->redirect("/blog");
        }
        else {
            return $this->render('back/Fournisseurs.html.twig',
                ['form'=>$form->Createview(),'users'=>$users,'us'=>$us,'userss'=>$userss]);
        }

        //  $form = $this->createForm(UtilisateurAddType::class,$user)
    }



    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/preferences", name="blogperf")
     */
    public function preferences(): Response
    {
        return $this->render('back/preferences.html.twig', [
            'controller_name' => 'BackaccController',
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/datav", name="blogdata")
     */
    public function datav(): Response
    {
        $user=$this->getUser();
        $produit=$this->getDoctrine()->getManager()->getRepository(Produits::class)->findAll();
        return $this->render('back/data-visualization.html.twig', [
            'controller_name' => 'BackaccController','us'=>$user, 'produit'=>$produit
        ]);
    }



    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route ("/reclamfour",name="ajouterreclamationsss")
     */
    function Addd(Request $request,\Swift_Mailer $mailer)
    {
        $user=$this->getUser();

        $reclamations=new Reclamations();
        $form=$this->createForm(ReclamationsType::class, $reclamations);
        $en=$this->getDoctrine()->getManager()->getRepository(Reclamations::class)->findAll();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->persist($reclamations);
            $em->flush();
            return $this->redirectToRoute('ajouterreclamationsss');
        }
        return $this->render('back/reclam.html.twig',
            [
                'form'=>$form->createView(), 'reclam'=>$en, 'us'=>$user
            ]
        );
    }




    /**
     * @Route("/statfour", name="blogdatalkl")
     */
    public function stat(): Response
    {
        $user=$this->getUser();
        $produit=$this->getDoctrine()->getManager()->getRepository(Produits::class)->findAll();
        return $this->render('back/statfour.html.twig', [
            'controller_name' => 'BackaccController','us'=>$user, 'produit'=>$produit
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/manage", name="blogmanage")
     */
    public function manage(): Response
    {
        $user=$this->getUser();
        return $this->render('back/manage-users.html.twig', [
            'controller_name' => 'BackaccController','us'=>$user
        ]);
    }



}
