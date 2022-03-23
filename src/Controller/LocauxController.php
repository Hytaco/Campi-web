<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Locaux;
use App\Entity\Programmes;
use App\Form\CommentaireType;
use App\Form\LocauxType;
use App\Repository\CommentaireRepository;
use App\Repository\LocauxRepository;
use App\Repository\ProgrammesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Endroid\QrCode\Builder\BuilderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class LocauxController extends AbstractController
{
    /**
     * @Route("/locauxx", name="locauxxx")
     */
    public function index(): Response
    {
        return $this->render('locaux/acceuil.htmltwig', [
            'controller_name' => 'LocauxController',
        ]);
    }
    /**
     * @Route("/commentairesf/{id}", name="loccommentairesfauxxf")
     */
    public function affcoms($id, Request $request): Response
    {
        $user = $this->getUser();
        $en=$this->getDoctrine()->getManager();
        $loc = $en->getRepository(Locaux::class)->find($id);
$coms = $en->getRepository(Commentaire::class)->findBy(['locaux'=>$loc]);
$commentaire = new Commentaire();
        $form=$this->createForm(CommentaireType::class, $commentaire);
        $commentaire->setUser($user);
        $commentaire->setLocaux($loc);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->persist($commentaire);
            $em->flush();
            return $this->redirectToRoute('loccommentairesfauxxf',['id'=>$id]);
        }
        return $this->render('front/frontacc/commentaires.html.twig', [
            'coms' => $coms,'form'=>$form->createView()
        ]);
    }
    /**
     * @Route("/locauxf", name="locauxxf")
     */
    public function afflocaux(): Response
    {
        $en=$this->getDoctrine()->getManager()->getRepository(Locaux::class)->findAll();

        return $this->render('front/locaux.html.twig', [
            'locx' => $en,
        ]);
    }
    /**
     * @Route("/supprimercom/{id}",name="supprimercom")
     */
    function Deletecom($id,CommentaireRepository $repository)
    {
        /** @var Commentaire $com */
        $com=$repository->find($id);
       $idl =  $com->getLocaux()->getId();
        $em=$this->getDoctrine()->getManager();
        $em->remove($com);
        $em->flush();//mise a jour
        return $this->redirectToRoute('loccommentairesfauxxf',['id'=>$idl]);
    }
    /**
     * @Route("/SupprimerLocaux/{id}",name="deletelocaux")
     */
    function Delete($id,LocauxRepository $repository)
    {
        $locaux=$repository->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove($locaux);
        $em->flush();//mise a jour
        return $this->redirectToRoute('ajouterlocaux');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route ("/locaux",name="ajouterlocaux")
     */
    function Add(BuilderInterface $customQrCodeBuilder,Request $request)
    {

        $locaux=new Locaux();
        $us= $this->getUser();
        $form=$this->createForm(LocauxType::class, $locaux);
        $en=$this->getDoctrine()->getManager()->getRepository(Locaux::class)->findAll();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->persist($locaux);
            $em->flush();
            return $this->redirectToRoute('ajouterlocaux');
        }
        if($request->isMethod("POST"))
        {
            $nom = $request->get('nom');
            $locaux=$this->getDoctrine()->getManager()->getRepository(Locaux::class)->findBy(array('nom'=>$nom));
            return $this->render('back/locaux.html.twig',
                [
                    'form'=>$form->createView(), 'loc'=>$locaux , 'locaux'=>$en,'us'=>$us
                ]
            );
        }
        return $this->render('back/locaux.html.twig',
            [
                'form'=>$form->createView(), 'loc'=>$en , 'locaux'=>$en,'us'=>$us
            ]
        );
    }















    /**
     * @Route("/GetLoc", name="GetLoc")
     */
    public function GetLoc(Request $request,NormalizerInterface $Normalizer)
    {/** @var Programmes $prog */
        $prog = $this->getDoctrine()->getManager()->getRepository(Programmes::class)->find($request->get('id'));
        $locaux =  $prog->getLocale();
       $jsonContent = $Normalizer->normalize($locaux,'json',['groups'=>'post:read']);


        return new Response(json_encode($jsonContent));
    }




    /**
     * @Route("/ShowLoc", name="display_locaux")
     */
    public function AfficherJSON(NormalizerInterface $Normalizer)
    {

        $locaux = $this->getDoctrine()->getManager()->getRepository(Locaux::class)->findAll();
        $jsonContent = $Normalizer->normalize($locaux,'json',['groups'=>'post:read']);


        return new Response(json_encode($jsonContent));
    }





    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route ("/AddLoc",name="ajouterlocaux2")
     */
    function AjouterJSON(NormalizerInterface $Normalizer,Request $request)
    {




        $em=$this->getDoctrine()->getManager();
        $locaux=new Locaux();

        $locaux->setNom($request->get('nom'));
        $locaux->setAdresse($request->get('adresse'));
        $locaux->setDescription($request->get('description'));
        $locaux->setImageName($request->get('imageName'));
        $locaux->setNote($request->get('note'));
        $locaux->setGoogleMap($request->get('googleMap'));

            $em->persist($locaux);
            $em->flush();
          $jsonContent = $Normalizer->normalize($locaux,'json',['groups'=>'post:read']);
          return new Response(json_encode($jsonContent));;



        return $this->render('back/locaux.html.twig');
    }




    /**
     * @Route("/UpdateLoc/{id}", name="update_loc")
     *
     */
    public function ModifierJSON(Request $request,$id,NormalizerInterface $Normalizer) {
        $em = $this->getDoctrine()->getManager();
        $locaux= $em->getRepository(Locaux::class)->find($id);

        $locaux->setNom($request->get('nom'));
        $locaux->setAdresse($request->get('adresse'));
        $locaux->setDescription($request->get('description'));
        $locaux->setImageName($request->get('imageName'));
        $locaux->setNote($request->get('note'));
        $locaux->setGoogleMap($request->get('googleMap'));


        $em->flush();
        $jsonContent = $Normalizer->normalize($locaux,'json',['groups'=>'post:read']);
        return new Response("Locale modifié".json_encode($jsonContent));;

    }


    /**
     * @Route("/DeleteLoc/{id}", name="delete_loc")
     *
     */
    public function SupprimerJSON(Request $request,$id,NormalizerInterface $Normalizer) {
        $em = $this->getDoctrine()->getManager();
        $locaux= $em->getRepository(Locaux::class)->find($id);


        $em->remove($locaux);

        $em->flush();
        $jsonContent = $Normalizer->normalize($locaux,'json',['groups'=>'post:read']);
        return new Response("Locale Supprimé".json_encode($jsonContent));;

    }





















    /**
     * @param Request $request
     * @Route("/ModifierLocaux/{id}",name="modifierlocaux")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    function modifier(LocauxRepository $repository,$id,Request $request)
    {
        $locaux=$repository->find($id);
        $us= $this->getUser();
        $form=$this->createForm(LocauxType::class,$locaux);
        $en=$this->getDoctrine()->getManager()->getRepository(Locaux::class)->findAll();
        $enn=$this->getDoctrine()->getManager()->getRepository(Locaux::class)->findAll();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('ajouterlocaux');
        }
        return $this->render('back/locaux.html.twig',
            [
                'form'=>$form->createView(), 'loc'=>$en,'us'=>$us, 'locaux'=>$enn
            ]
        );
    }

    /**
     * @Route("tlocauxdesc",name="tlocauxdesc")
     */
    public function trisaldesc(LocauxRepository $repo, Request $request)
    {

        $articles =
            $repo->trisaldesc();
$locaux = new Locaux();
        $us= $this->getUser();
        $form=$this->createForm(LocauxType::class, $locaux);
        $en=$this->getDoctrine()->getManager()->getRepository(Locaux::class)->findAll();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->persist($locaux);
            $em->flush();
            return $this->redirectToRoute('ajouterlocaux');
        }
        if($request->isMethod("POST"))
        {
            $nom = $request->get('nom');
            $locaux=$this->getDoctrine()->getManager()->getRepository(Locaux::class)->findBy(array('nom'=>$nom));
            return $this->render('back/locaux.html.twig',
                [
                    'form'=>$form->createView(), 'loc'=>$locaux , 'locaux'=>$articles,'us'=>$us
                ]
            );
        }
        return $this->render('back/locaux.html.twig',
            [
                'form'=>$form->createView(), 'loc'=>$articles , 'locaux'=>$en,'us'=>$us
            ]
        );
    }

    /**
     * @Route("tlocauxasc",name="tlocauxasc")
     */
    public function trisalasc(LocauxRepository $repo, Request $request)
    {

        $articles =
            $repo->trisalasc();
        $locaux = new Locaux();
        $us= $this->getUser();
        $form=$this->createForm(LocauxType::class, $locaux);
        $en=$this->getDoctrine()->getManager()->getRepository(Locaux::class)->findAll();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->persist($locaux);
            $em->flush();
            return $this->redirectToRoute('ajouterlocaux');
        }
        if($request->isMethod("POST"))
        {
            $nom = $request->get('nom');
            $locaux=$this->getDoctrine()->getManager()->getRepository(Locaux::class)->findBy(array('nom'=>$nom));
            return $this->render('back/locaux.html.twig',
                [
                    'form'=>$form->createView(), 'loc'=>$locaux , 'locaux'=>$articles,'us'=>$us
                ]
            );
        }
        return $this->render('back/locaux.html.twig',
            [
                'form'=>$form->createView(), 'loc'=>$articles , 'locaux'=>$en,'us'=>$us
            ]
        );

    }



}
