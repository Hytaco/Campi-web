<?php

namespace App\Controller;

use App\Entity\Alerts;
use App\Form\AlertsType;
use App\Repository\AlertsRepository;
use Doctrine\Persistence\ObjectManager;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Contracts\Translation\TranslatorInterface;
use  Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class AlertsController extends AbstractController
{

 /**
 * @Route("/ShowAlerts", name="display_alerts")
 */
    public function AfficherJSON(NormalizerInterface $Normalizer)
    {
        $us= $this->getUser();
        $en=$this->getDoctrine()->getManager()->getRepository(Alerts::class)->findAll();
        $locaux = $this->getDoctrine()->getManager()->getRepository(Alerts::class)->findAll();
        $jsonContent = $Normalizer->normalize($locaux,'json',['groups'=>'post:read']);

        return new Response(json_encode($jsonContent));;
    }





    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route ("/AddAlerts",name="ajouterAlerts")
     */
    function AjouterJSON(NormalizerInterface $Normalizer,Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $Alerts=new Alerts();
        $Alerts->setLocalisation($request->get('localisation'));
        $Alerts->setDate(new \DateTime());
        $Alerts->setRapport($request->get('rapport'));
        $Alerts->setTelephone($request->get('telephone'));
        $Alerts->setMail($request->get('mail'));
        $em->persist($Alerts);
        $em->flush();
        $jsonContents = $Normalizer->normalize($Alerts,'json',['groups'=>'post:read']);
        return new Response(json_encode($jsonContents));;

        return $this->render('back/alertes.html.twig');
    }




    /**
     * @Route("/UpdateAlerts/{id}", name="update_Alerts")
     *
     */
    public function ModifierJSON(Request $request,$id,NormalizerInterface $Normalizer) {
        $em = $this->getDoctrine()->getManager();
        $Alerts= $em->getRepository(Alerts::class)->find($id);

        $Alerts->setLocalisation($request->get('localisation'));
        $Alerts->setDate($request->get('date'));
        $Alerts->setRapport($request->get('rapport'));
        $Alerts->setTelephone($request->get('telephone'));
        $Alerts->setMail($request->get('mail'));
        $Alerts->setProgramme($request->get('programme'));




        $em->flush();
        $jsonContent = $Normalizer->normalize($Alerts,'json',['groups'=>'post:read']);
        return new Response("Alerts modifié".json_encode($jsonContent));;

    }


    /**
     * @Route("/DeleteAlerts/{id}", name="update_Alerts")
     *
     */
    public function SupprimerJSON(Request $request,$id,NormalizerInterface $Normalizer) {
        $em = $this->getDoctrine()->getManager();
        $Alerts= $em->getRepository(Alerts::class)->find($id);


        $em->remove($Alerts);

        $em->flush();
        $jsonContent = $Normalizer->normalize($Alerts,'json',['groups'=>'post:read']);
        return new Response("Alerts Supprimé".json_encode($jsonContent));;

    }


    /**
     * @var FlashyNotifier
     */
    private $flashy;

    /**
     * @Route("/alertss", name="afficheralertes")
     */
    public function index(): Response
    {
        return $this->render('back/alertes.html.twig', [
            'controller_name' => 'AlertsController',
        ]);
    }

    /**
     * @Route("/SupprimerAlertes/{id}",name="deletealertes")
     */
    function Delete($id,AlertsRepository $repository)
    {
        $alertes=$repository->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove($alertes);
        $em->flush();//mise a jour
        return $this->redirectToRoute('afficheralerts');
    }

    /**
     *@IsGranted("ROLE_ADMIN")
     * @Route("/alerts",name="afficheralerts")
     */
    public function Affiche(AlertsRepository $repository)
    {
        $user=$this->getUser();
        $alerts=$repository->findAll();
        return $this->render('back/alertes.html.twig',
            ['aler'=>$alerts, 'us'=>$user]);
    }


    /**
     * @Route("/alertsff", name="alertesfff")
     */
    public function alets(TranslatorInterface $translator): Response
    {
        return $this->render('front/alertes.html.twig', [
            'controller_name' => 'FrontaccController',
        ]);
    }



    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route ("/alertsf",name="ajouteralertes")
     */
    function Add(Request $request,\Swift_Mailer $mailer,TranslatorInterface $translator,FlashyNotifier $flashy)
    {
        $user=$this->getUser();
        $alerts=new Alerts();
        $this->flashy= $flashy;

        $form=$this->createForm(AlertsType::class, $alerts);
        $en=$this->getDoctrine()->getManager()->getRepository(Alerts::class)->findAll();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $alerts->setDate(new \DateTime());
            $em=$this->getDoctrine()->getManager();
            $em->persist($alerts);
            $message = (new \Swift_Message('Alerte!'))
                ->setFrom('HYTACOCAMPII@gmail.com')
                ->setTo($alerts->getMail())
                ->setBody(
                    'Par cet email présent nous vous proposons ces numéros pour vous aider: 
                193          : Garde nationale.
                197          : Police nationale.
                198          : Protection civile.
                801111      : numéro vert.'
                );
            $mailer->send($message);
            $em->flush();
            $this->addFlash('success','Votre alert est bien reçu, Verifier votre boite mail svp! Merci.');
            $this->flashy->success('ALERT AJOUTE!', 'http://your-awesome-link.com');
            return $this->redirectToRoute('ajouteralertes');
        }
        return $this->render('front/alertes.html.twig',
            [
                'form'=>$form->createView(), 'aler'=>$en
            ]
        );
    }




}
