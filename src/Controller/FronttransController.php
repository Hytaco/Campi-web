<?php

namespace App\Controller;

use App\Entity\Transporteur;
use App\Form\TransporteurType;
use App\Repository\TransporteurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
class FronttransController extends AbstractController
{  /**
 * @Route("/ShowLoc", name="display_locaux")
 */
    public function AfficherJSON(NormalizerInterface $Normalizer)
    {
        $us= $this->getUser();
        $en=$this->getDoctrine()->getManager()->getRepository(Locaux::class)->findAll();
        $locaux = $this->getDoctrine()->getManager()->getRepository(Locaux::class)->findAll();
        $jsonContent = $Normalizer->normalize($locaux,'json',['groups'=>'post:read']);


        return new Response(json_encode($jsonContent));;
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
     * @Route("/DeleteLoc/{id}", name="update_loc")
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
     * @Route("/fronttrans", name="fronttrans")
     */
    public function index(): Response
    {
        return $this->render('consult/index.html.twig', [
            'controller_name' => 'FronttransController',
        ]);
    }
    /**

     * @return \Symfony\Component\HttpFoundation\Response
     * @Route ("/fronttransds",name="transaffiched")
     */
    function affiche()
    {
        $en=$this->getDoctrine()->getManager()->getRepository(Transporteur::class)->findAll();
return $this->render('front/consult.html.twig',
[
'trans'=>$en
]
);}
}
