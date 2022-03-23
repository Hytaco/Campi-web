<?php

namespace App\Controller;

use App\Entity\Reclamations;
use App\Form\ReclamationsType;
use App\Repository\ReclamationsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Rest\Client as Client;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
class ReclamationsController extends AbstractController
{




    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route ("/AddReclamations",name="ajoutereclamations")
     */
    function AjouterJSON(NormalizerInterface $Normalizer,Request $request)
    {


        $em=$this->getDoctrine()->getManager();
        $reclamations=new Reclamations();

        $reclamations->setType($request->get('type'));
        $reclamations->setDescription($request->get('description'));
        $reclamations->setEmail($request->get('email'));

        $em->persist($reclamations);
        $em->flush();
        $jsonContent = $Normalizer->normalize($reclamations,'json',['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));;



        return $this->render('back/reclamations.html.twig');
    }



    /**
     * @Route("/reclamationss", name="reclamationsss")
     */
    public function index(): Response
    {
        return $this->render('reclamations/acceuil.htmltwig', [
            'controller_name' => 'ReclamationsController',
        ]);
    }
    /**
     * @Route("/SupprimerReclamations/{id}",name="deletereclamations")
     */
    function Delete($id,ReclamationsRepository $repository)
    {
        $reclamations=$repository->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove($reclamations);
        $em->flush();//mise a jour
        return $this->redirectToRoute('ajouterreclamations');
    }



    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route ("/reclamation",name="ajouterreclamations")
     */
    function Add(Request $request)
    {
        $reclamations=new Reclamations();
        $user=$this->getUser();

        $form=$this->createForm(ReclamationsType::class, $reclamations);
        $en=$this->getDoctrine()->getManager()->getRepository(Reclamations::class)->findAll();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->persist($reclamations);
            $em->flush();
            return $this->redirectToRoute('ajouterreclamations');
        }
        return $this->render('back/reclamations.html.twig',
            [
                'form'=>$form->createView(), 'reclam'=>$en,'us'=>$user
            ]
        );
    }

    /**
     * @param Request $request
     * @Route("/ModifierReclamations/{id}",name="modifierreclamations")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    function modifier(ReclamationsRepository $repository,$id,Request $request)
    {
        $reclamations=$repository->find($id);
        $user=$this->getUser();

        $form=$this->createForm(ReclamationsType::class,$reclamations);
        $en=$this->getDoctrine()->getManager()->getRepository(Reclamations::class)->findAll();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('ajouterreclamations');
        }
        return $this->render('back/reclamations.html.twig',
            [
                'form'=>$form->createView(), 'reclam'=>$en,'us'=>$user
            ]
        );
    }
    /**
     *  @Route("/statR", name="SR")
     */

    public function static (ReclamationsRepository $repo)
    {
        $ob = new Highchart();
        $ob->chart->renderTo('linechart');
        $ob->title->text('Statistique par rapport au type du réclamation client 2021');
        $ob->plotOptions->pie(array(
            'allowPointSelect'  => true,
            'cursor'    => 'pointer',
            'dataLabels'    => array('enabled' => false),
            'showInLegend'  => true
        ));
        $offre=$repo->stat1();
        $data =array();
        foreach ($offre as $values)
        {
            $a =array($values['type'],intval($values['nbdep']));
            array_push($data,$a);
        }

        $ob->series(array(array('type' => 'pie','name' => 'Browser share', 'data' => $data)));
        return $this->render('back/reclamationsChart.html.twig', array(
            'chart' => $ob
        ));}
}
