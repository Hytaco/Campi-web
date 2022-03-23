<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Evenements;
use App\Entity\Livreurs;
use App\Entity\Programmes;
use App\Entity\Proposition;
use App\Entity\Utilisateur;
use App\Form\EvenementsType;
use App\Form\LivreursType;
use App\Form\ProgrammesType;
use App\Form\PropositionType;
use App\Repository\EvenementsRepository;
use App\Repository\LivreursRepository;
use App\Repository\ProgrammesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;
use App\Entity\Locaux;
use Doctrine\ORM\EntityManagerInterface;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;
use App\Entity\Transporteur;
use App\Form\TransporteurType;
use App\Repository\TransporteurRepository;
use App\Entity\Reclamations;
use App\Form\ReclamationsType;
use App\Repository\PostlikRepository;
use App\Entity\Postlik;
use Symfony\Component\Form\FormTypeInterface;
use Twilio\Rest\Client as Client;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
class FrontaccController extends AbstractController
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
     * @Route("/accueil", name="frontacc")
     */
    public function ajouterReclamation(Request $request,\Swift_Mailer $mailer): Response
    { $reclamations=new Reclamations();
        $enl=$this->getDoctrine()->getManager()->getRepository(Locaux::class)->findAll();

        $en=$this->getDoctrine()->getManager()->getRepository(Categories::class)->findAll();

        $form=$this->createForm(ReclamationsType::class, $reclamations);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->persist($reclamations);
            $message = (new \Swift_Message('Madame, Monsieur, '))
                ->setFrom('hytacocampi21@gmail.com')
                ->setTo($reclamations->getEmail())
                ->setBody(
                    'Nous avons bien pris en compte votre réclamation. soyons assuré que cet incident demeure tout a fait exceptionnel et totalement indépendant de notre volonté, nous en sommes convaincue, devrait répondre a vos exigences.'
                )
            ;
            $mailer->send($message);
            $em->flush();
            return $this->redirectToRoute('frontacc');

        }
        return $this->render('front/acceuil.html.twig',
            [
                'form'=>$form->createView() , 'cat' => $en,'locx'=>$enl
            ]
        );

    }

    /**
     * @Route("/visitorf", name="frontcomptee")
     */
    public function visitor(): Response
    {
        return $this->render('front/visitor.html.twig', [
            'controller_name' => 'FrontaccController',
        ]);
    }

    /**
     * @Route("/affichef", name="frontaffichage")
     */
    public function affiche(): Response
    {
        return $this->render('front/consult.html.twig', [
            'controller_name' => 'FrontaccController',
        ]);
    }

    /**
     * @Route("/contactf", name="fcontact")
     */
    public function contact(): Response
    {
        $httpClient = new \Http\Adapter\Guzzle6\Client();
        $provider = new \Geocoder\Provider\GoogleMaps\GoogleMaps($httpClient, null, 'AIzaSyB0zkek9cGyGPwkFIYy8uNhbzppD_s4gpE');
        $geocoder = new \Geocoder\StatefulGeocoder($provider, 'en');
        $result = $geocoder->geocodeQuery(GeocodeQuery::create('Buckingham Palace, London'));
        return $this->render('front/contact.html.twig', [
            'controller_name' => 'FrontaccController',
        ]);
    }


    /**
     * @Route("/connexionf", name="frontconnect")
     */
    public function connexion(): Response
    {
        return $this->render('front/connexion.html.twig', [
            'controller_name' => 'FrontaccController',
        ]);
    }

    /**
     * @Route("/commandef", name="frontcommande")
     */
    public function commandes(): Response
    {
        return $this->render('front/commandes.html.twig', [
            'controller_name' => 'FrontaccController',
        ]);
    }




    /**
     * @Route("/profilef", name="frontprofile")
     */
    public function profile(): Response
    {
        return $this->render('front/profile.html.twig', [
            'controller_name' => 'FrontaccController',
        ]);
    }



    /**
     * @Route("/commentairef", name="frontcommentaire")
     */

    public function AjouterCommentaire(Request $request,CommentaireRepository $comp)
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $coms = $comp->findAll();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($commentaire);
            $em->flush();
            return $this->redirectToRoute('frontcommentaire');
        }
        return $this->render('front/frontacc/commentaires.html.twig', ['form' => $form->createView(),'coms'=>$coms
        ]);
    }


    /**
     * @Route ("/post/{id}/lik",name="post_lik")
     * @param Commentaire $commentaire
     * @param PostlikRepository $likerepo
     *
     * @return Response
     */
    public function lik (Commentaire $commentaire,PostlikRepository $likerepo,EntityManagerInterface $entityManager):Response
    { $user=$this->getUser();
        if(!$user) return $this->json([
            'code'=> 403,
            'message'=> "unsuthorised"
        ],403);

        if($commentaire->islikedByUser($user)){
            $lik=$likerepo->findOneBy([
                'post'=>$commentaire,
                'user'=>$user
            ]);
            $entityManager->remove($lik);
            $entityManager->flush();
            return $this->json(
                [
                    'code'=> 200,
                    'message'=>'like bien supprime',
                    'likes'=>$likerepo->count(['post'=>$commentaire])
                ],200

            );
        }
        $lik =new Postlik();
        $lik->setPost($commentaire)
            ->setUser($user);
        $entityManager->persist($lik);
        $entityManager->flush();




        return $this->json(['code'=> 200, 'message'=>'clike bien ajoute','likes'=>$likerepo->count(['post'=>$commentaire])],200);

    }



    /**
     * @Route("/supprimer{id}", name="supprimer")
     */
    public function delete (CommentaireRepository $comp,$id,  EntityManagerInterface $entityManager){
        $com=  $comp->find($id);
        $entityManager->remove($com);
        $entityManager->flush();
        return $this->redirectToRoute('frontcommentaire');
    }


    /**
     * @Route("/selectionprog/{id}", name="selectionprog")
     */
    public function selectionprog(ProgrammesRepository  $rep,$id): Response
    {   $prog=$rep->find($id);
        return $this->render('front/selectionprog.html.twig', [
            'p' => $prog
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route ("/proposition",name="prFposition")
     */
    function Proposition(Request $request,\Swift_Mailer $mailer)
    {
        $transporteur=new Transporteur();
        $proposition=new Proposition();
        $livreur=new Livreurs();
        $programme=new Programmes();

        $form=$this->createForm(TransporteurType::class, $transporteur);
        $form1=$this->createForm(PropositionType::class,$proposition);
        $form2=$this->createForm(LivreursType::class,$livreur);
        $form3=$this->createForm(ProgrammesType::class,$programme);
$users=$this->getDoctrine()->getManager()->getRepository(Utilisateur::class)->findByRole('ROLE_ADMIN');
        $form->handleRequest($request);
        $form1->handleRequest($request);
        $form2->handleRequest($request);
        $form3->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {


            foreach ($users as $user) {
                $message = (new \Swift_Message('Demande Transporteur'))
                    ->setFrom('HYTACOCAMPII@gmail.com')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView('front/demande.html.twig',
                            ['type' => 'transporteur', 'nom' => $transporteur->getNom(), 'adresse' => $transporteur->getAdresse(),
                                'numero' => $transporteur->getNumero(), 'mail' => $transporteur->getMail(), 'typet' => $transporteur->getType()]
                        ),
                        'text/html'
                    );
                $status = $mailer->send($message);
            }
                return $this->redirectToRoute('transaffiched');
        }

        if($form1->isSubmitted() && $form1->isValid())
        {
            $em1=$this->getDoctrine()->getManager();
            $em1->persist($proposition);


            $message1 = (new \Swift_Message('Bienvenue'))
                ->setFrom('HYTACOCAMPII@gmail.com')
                ->setTo($proposition->getMail())
                ->setBody(
                    'Bienvenue, votre proposition est bien recu!'
                )
            ;
            $mailer->send($message1);
            $em1->flush();
            return $this->redirectToRoute('affiche');

        }


        if($form2->isSubmitted() && $form2->isValid())
        {
            foreach ($users as $user) {
                $message = (new \Swift_Message('Demande livreur'))
                    ->setFrom('HYTACOCAMPII@gmail.com')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView('front/demande.html.twig',
                            ['type' => 'livreurs', 'nom' => $livreur->getNom(), 'adresse' => $livreur->getAdresse(),
                                'telephone' => $livreur->getTelephone(), 'mail' => $livreur->getMail()]
                        ),
                        'text/html'
                    );
                $status = $mailer->send($message);
            }
                return $this->redirectToRoute('frontacc');
            }


        if($form3->isSubmitted() && $form3->isValid())
        {
            $em3=$this->getDoctrine()->getManager();
            $em3->persist($programme);


            $message3 = (new \Swift_Message('Bienvenue'))
                ->setFrom('HYTACOCAMPII@gmail.com')
                ->setTo($programme->getTransporteur()->getMail())
                ->setBody(
                    'Bienvenue, votre proposition est bien recu!'
                )
            ;
            $mailer->send($message3);
            $em3->flush();
            return $this->redirectToRoute('programmesf');

        }

        return $this->render('front/propositions.html.twig',
            [
                'form'=>$form->createView(), 'form1'=>$form1->createView(), 'form2'=>$form2->createView(), 'form3'=>$form3->createView()
            ]
        );

    }

}
