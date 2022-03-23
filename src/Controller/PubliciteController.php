<?php

namespace App\Controller;

use App\Entity\Publicite;
use App\Form\PubliciteType;
use App\Repository\PubliciteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use  Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
class PubliciteController extends AbstractController
{  /**
 * @Route("/ShowPublicite", name="display_Publicite")
 */
    public function AfficherJSON(NormalizerInterface $Normalizer)
    {
        $us= $this->getUser();
        $en=$this->getDoctrine()->getManager()->getRepository(Publicite::class)->findAll();
        $Publicite = $this->getDoctrine()->getManager()->getRepository(Publicite::class)->findAll();
        $jsonContent = $Normalizer->normalize($Publicite,'json',['groups'=>'post:read']);


        return new Response(json_encode($jsonContent));;
    }





    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route ("/AddPublicite",name="ajouterPublicite")
     */
    function AjouterJSON(NormalizerInterface $Normalizer,Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $Publicite=new Publicite();

        $Publicite->setNom($request->get('nom'));
        $Publicite->setLien($request->get('lien'));
        $Publicite->setImageName($request->get('imageName'));

        $em->persist($Publicite);
        $em->flush();
        $jsonContent = $Normalizer->normalize($Publicite,'json',['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));;


        return $this->render('back/publicite.html.twig');
    }




    /**
     * @Route("/Updatepublicite/{id}", name="update_publicite")
     *
     */
    public function ModifierJSON(Request $request,$id,NormalizerInterface $Normalizer) {
        $em = $this->getDoctrine()->getManager();
        $publicite= $em->getRepository(Publicite::class)->find($id);

        $publicite->setNom($request->get('nom'));
        $publicite->setLien($request->get('lien'));
        $publicite->setImageName($request->get('imageName'));


        $em->flush();
        $jsonContent = $Normalizer->normalize($publicite,'json',['groups'=>'post:read']);
        return new Response("publicite modifié".json_encode($jsonContent));;

    }


    /**
     * @Route("/Deletepublicite/{id}", name="update_publicite")
     *
     */
    public function SupprimerJSON(Request $request,$id,NormalizerInterface $Normalizer) {
        $em = $this->getDoctrine()->getManager();
        $publicite= $em->getRepository(Publicite::class)->find($id);


        $em->remove($publicite);

        $em->flush();
        $jsonContent = $Normalizer->normalize($publicite,'json',['groups'=>'post:read']);
        return new Response("publicite Supprimé".json_encode($jsonContent));;

    }

    /**
     * @Route("/publicitee", name="publiciteeee")
     */
    public function index(): Response
    {
        return $this->render('publicite/index.html.twig', [
            'controller_name' => 'PubliciteController',
        ]);
    }



    /**
     * @Route("/SupprimerPublicite/{id}",name="deletepublicite")
     */
    function Delete($id,PubliciteRepository $repository)
    {
        $publicite=$repository->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove($publicite);
        $em->flush();//mise a jour
        return $this->redirectToRoute('ajouterpublicite');
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route ("/publicite",name="ajouterpublicite")
     */
    function Add(Request $request)
    {
        $publicite=new Publicite();
        $user=$this->getUser();
        $form=$this->createForm(PubliciteType::class, $publicite);
        $en=$this->getDoctrine()->getManager()->getRepository(Publicite::class)->findAll();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->persist($publicite);
            $em->flush();
            return $this->redirectToRoute('ajouterpublicite');
        }
        return $this->render('back/publicite.html.twig',
            [
                'form'=>$form->createView(), 'pub'=>$en, 'us'=>$user
            ]
        );
    }


    /**
     * @param Request $request
     * @Route("/ModifierPublicite/{id}",name="modifierpublicite")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    function modifier(PubliciteRepository $repository,$id,Request $request)
    {
        $publicite=$repository->find($id);
        $user=$this->getUser();
        $form=$this->createForm(PubliciteType::class,$publicite);
        $en=$this->getDoctrine()->getManager()->getRepository(Publicite::class)->findAll();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('ajouterpublicite');
        }
        return $this->render('back/publicite.html.twig',
            [
                'form'=>$form->createView(), 'pub'=>$en,'uss'=>$user,'us'=>$user
            ]
        );
    }
}
