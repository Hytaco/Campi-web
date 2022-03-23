<?php

namespace App\Controller;

use App\Entity\Proposition;
use App\Form\PropositionType;
use App\Repository\PropositionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use  Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
class PropositionController extends AbstractController
{


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route ("/Addproposition",name="ajouterproposition")
     */
    function AjouterJSON(NormalizerInterface $Normalizer,Request $request)
    {

        $em=$this->getDoctrine()->getManager();
        $proposition=new Proposition();

        $proposition->setNom($request->get('nom'));
        $proposition->setDate(new \DateTime(urldecode($request->get('date'))));
        $proposition->setNombrePlace($request->get('nombre_place'));
        $proposition->setMail($request->get('mail'));

        $em->persist($proposition);
        $em->flush();
        $jsonContent = $Normalizer->normalize($proposition,'json',['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));;



        return $this->render('back/proposition.html.twig');
    }



    /**
     * @Route("/proposition", name="proposition")
     */
    public function index(): Response
    {
        return $this->render('proposition/index.html.twig', [
            'controller_name' => 'PropositionController',
        ]);
    }

    /**
     * @param PropositionRepository $repository
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/afficheP", name="afficheP")
     */
    public function Affichage(PropositionRepository $repository)
    {
        //$en=$this->getDoctrine()->getManager()->getRepository(Evenements::class)->findAll();
        // var_dump($en);
        $en = $repository->findAll();
        return $this->render('back/proposition.html.twig ',
            ['formations' => $en]);
    }

    /**
     * @Route("/propositionf", name="proposition")
     */
    public function AjouterProposition(Request $request,\Swift_Mailer $mailer)
    {
        $user = $this->getUser();
        $en = $this->getDoctrine()->getManager()->getRepository(Proposition::class)->findAll();
        $proposition = new Proposition();
        $form = $this->createForm(PropositionType::class, $proposition);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($proposition);
            $message = (new \Swift_Message('Mail de confirmation'))
                ->setFrom('HYTACOCAMPII@gmail.com')->setTo($proposition->getMail())->setBody(
                    'Votre Proposition est bien reçue! Merci.'
                );
            $mailer->send($message);
            $em->flush();
            $this->addFlash(
                'info',
                'Added successfuly'
            );
            return $this->redirectToRoute('proposition');
        }
        return $this->render('front/proposition.html.twig', ['form' => $form->createView(), 'formations' => $en, 'us' => $user
        ]);
    }



    /**
     * @Route("/suppressionp{id}", name="suppressionp")
     */
    public function suppression(Proposition $proposition, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($proposition);
        $entityManager->flush();
        $this->addFlash(
            'info',
            'Deleted successfuly'
        );
        return $this->redirectToRoute('afficheP');
    }






    /**
     * @param Request $request
     * @Route("/ModifierProposition/{id}",name="modifierproposition")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    function modifier(PropositionRepository $repository,$id,Request $request)
    {
        $user = $this->getUser();
        $proposition=$repository->find($id);
        $form = $this->createForm(PropositionType::class, $proposition);
        $en = $this->getDoctrine()->getManager()->getRepository(Proposition::class)->findAll();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash(
                'info',
                'Edited successfuly'
            );
            return $this->redirectToRoute('afficheP');
        }
        return $this->render('back/proposition.html.twig',
            [
                'form' => $form->createView(), 'formations' => $en, 'us' => $user
            ]
        );

    }

}
