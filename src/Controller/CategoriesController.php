<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use  Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class CategoriesController extends AbstractController
{

    /**
     * @Route("/Showcategorie", name="display_categorie")
     */
    public function AfficherJSON(NormalizerInterface $Normalizer)
    {
        $us= $this->getUser();
        $en=$this->getDoctrine()->getManager()->getRepository(Categories::class)->findAll();
        $categorie = $this->getDoctrine()->getManager()->getRepository(Categories::class)->findAll();
        $jsonContent = $Normalizer->normalize($categorie,'json',['groups'=>'post:read']);


        return new Response(json_encode($jsonContent));
    }





    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route ("/Addcategorie",name="ajoutercategorie")
     */
    function AjouterJSON(NormalizerInterface $Normalizer,Request $request)
    {




        $em=$this->getDoctrine()->getManager();
        $categorie=new Categories();

        $categorie->setNom($request->get('nom'));
        $categorie->setDescription($request->get('description'));
        $categorie->setImageName($request->get('imageName'));

        $em->persist($categorie);
        $em->flush();
        $jsonContent = $Normalizer->normalize($categorie,'json',['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));



        return $this->render('back/categories.html.twig');
    }




    /**
     * @Route("/Updatecategorie/{id}", name="update_categorie")
     *
     */
    public function ModifierJSON(Request $request,$id,NormalizerInterface $Normalizer) {
        $em = $this->getDoctrine()->getManager();
        $categorie= $em->getRepository(Categories::class)->find($id);

        $categorie->setNom($request->get('nom'));
        $categorie->setDescription($request->get('description'));
        $categorie->setImageName($request->get('imageName'));

        $em->flush();
        $jsonContent = $Normalizer->normalize($categorie,'json',['groups'=>'post:read']);
        return new Response("categorie modifié".json_encode($jsonContent));

    }


    /**
     * @Route("/Deletecategorie/{id}", name="update_categorie")
     *
     */
    public function SupprimerJSON(Request $request,$id,NormalizerInterface $Normalizer) {
        $em = $this->getDoctrine()->getManager();
        $categorie= $em->getRepository(Categories::class)->find($id);


        $em->remove($categorie);

        $em->flush();
        $jsonContent = $Normalizer->normalize($categorie,'json',['groups'=>'post:read']);
        return new Response("categorie Supprimé".json_encode($jsonContent));

    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/categoriess", name="categoriess")
     */
    public function index(): Response
    {
        return $this->render('back/categories.html.twig', [
            'controller_name' => 'CategoriesController',
        ]);
    }

    /**
     * @Route("/SupprimerCategorie/{id}",name="supprimerc")
     */
    function Delete($id,CategoriesRepository $repository)
    {
        $categories=$repository->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove($categories);
        $em->flush();//mise a jour
        return $this->redirectToRoute('ajoutercategories');
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route ("/categorie",name="ajoutercategories")
     */
    function Add(Request $request)
    {
        $categories=new Categories();
        $user=$this->getUser();
        $form=$this->createForm(CategoriesType::class, $categories);
        $en=$this->getDoctrine()->getManager()->getRepository(Categories::class)->findAll();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->persist($categories);
            $em->flush();
            return $this->redirectToRoute('ajoutercategories');
        }
        return $this->render('back/categories.html.twig',
            [
                'form'=>$form->createView(), 'cat'=>$en, 'us'=>$user
            ]
        );
    }


    /**
     * @param Request $request
     * @Route("/ModifierCategories/{id}",name="modifiercategories")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    function modifier(CategoriesRepository $repository,$id,Request $request)
    {
        $categories=$repository->find($id);
        $user=$this->getUser();
        $form=$this->createForm(CategoriesType::class,$categories);
        $en=$this->getDoctrine()->getManager()->getRepository(Categories::class)->findAll();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('ajoutercategories');
        }
        return $this->render('back/categories.html.twig',
            [
                'form'=>$form->createView(), 'cat'=>$en,'uss'=>$user,'us'=>$user
            ]
        );
    }

}
