<?php

namespace App\Controller;

use App\Entity\Livraisons;
use App\Entity\Livreurs;
use App\Form\LivraisonsType;
use App\Form\LivreursType;
use App\Repository\LivreursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class LivreursController extends AbstractController
{
    /**
     * @Route("/ShowLiv", name="display_livreur")
     */
    public function AfficherJSON(NormalizerInterface $Normalizer)
    {
        $us= $this->getUser();
        $en=$this->getDoctrine()->getManager()->getRepository(Livreurs::class)->findAll();
        $livreur = $this->getDoctrine()->getManager()->getRepository(Livreurs::class)->findAll();
        $jsonContent = $Normalizer->normalize($livreur,'json',['groups'=>'post:read']);


        return new Response(json_encode($jsonContent));;
    }





    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route ("/AddLiv",name="ajouterlivreur2")
     */
    function AjouterJSON(NormalizerInterface $Normalizer,Request $request)
    {

        $em=$this->getDoctrine()->getManager();
        $livreur=new Livreurs();

        $livreur->setTelephone($request->get('telephone'));
        $livreur->setAdresse($request->get('adresse'));
        $livreur->setMail($request->get('mail'));
        $livreur->setNom($request->get('nom'));

        $em->persist($livreur);
        $em->flush();
        $jsonContent = $Normalizer->normalize($livreur,'json',['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));;

        return $this->render('back/livreurs.html.twig');
    }




    /**
     * @Route("/UpdateLiv/{id}", name="update_liv")
     *
     */
    public function ModifierJSON(Request $request,$id,NormalizerInterface $Normalizer) {
        $em = $this->getDoctrine()->getManager();
        $livreur= $em->getRepository(Livreurs::class)->find($id);

        $livreur->setTelephone($request->get('telephone'));
        $livreur->setAdresse($request->get('adresse'));
        $livreur->setMail($request->get('mail'));
        $livreur->setNom($request->get('nom'));
        $livreur->setLivraisons($request->get('livraisons'));


        $em->flush();
        $jsonContent = $Normalizer->normalize($livreur,'json',['groups'=>'post:read']);
        return new Response("Livreur modifié".json_encode($jsonContent));;

    }


    /**
     * @Route("/DeleteLiv/{id}", name="update_liv")
     *
     */
    public function SupprimerJSON(Request $request,$id,NormalizerInterface $Normalizer) {
        $em = $this->getDoctrine()->getManager();
        $livreur= $em->getRepository(Livreurs::class)->find($id);


        $em->remove($livreur);

        $em->flush();
        $jsonContent = $Normalizer->normalize($livreur,'json',['groups'=>'post:read']);
        return new Response("Livreur Supprimé".json_encode($jsonContent));;

    }

    /**
     * @Route("/livreurss", name="livreursss")
     */
    public function index(): Response
    {
        return $this->render('back/livreurs.html.twig', [
            'controller_name' => 'LivreursController',
        ]);
    }
    /**
     * @Route("/Supprimerlivreurs/{id}",name="deletelivreurs")
     */
    function Delete($id,LivreursRepository $repository)
    {
        $livreurs=$repository->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove($livreurs);
        $em->flush();//mise a jour

        return $this->redirectToRoute('ajouterlivreurs');
    }



    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route ("/livreur",name="ajouterlivreurs")
     */
    function Add(Request $request)
    {
        $livreurs=new Livreurs();
        $form=$this->createForm(LivreursType::class, $livreurs);
        $en=$this->getDoctrine()->getManager()->getRepository(Livreurs::class)->findAll();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->persist($livreurs);
            $em->flush();
            return $this->redirectToRoute('ajouterlivreurs');

        }
        if($request->isMethod("POST"))
        {
            $nom = $request->get('nom');
            $livreurs=$this->getDoctrine()->getManager()->getRepository(Livreurs::class)->findBy(array('nom'=>$nom));
            return $this->render('back/livreurs.html.twig',
                [
                    'form'=>$form->createView(), 'liv'=>$livreurs
                ]
            );
        }

        return $this->render('back/livreurs.html.twig',
            [
                'form'=>$form->createView(), 'liv'=>$en
            ]
        );
    }


    /**
     * @param Request $request
     * @Route("/Modifierlivreurs/{id}",name="modifierlivreurs")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    function modifier(LivreursRepository $repository,$id,Request $request)
    {
        $livreurs=$repository->find($id);
        $form=$this->createForm(LivreursType::class,$livreurs);
        $en=$this->getDoctrine()->getManager()->getRepository(Livreurs::class)->findAll();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('ajouterlivreurs');
        }
        return $this->render('back/livreurs.html.twig',
            [
                'form'=>$form->createView(), 'liv'=>$en
            ]
        );
    }
}
