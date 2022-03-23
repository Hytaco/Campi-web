<?php

namespace App\Controller;

use App\Entity\Evenements;
use App\Entity\PostLikes;
use App\Entity\Sponsors;
use App\Form\EvenementsType;
use App\Repository\PostLikesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Repository\RepositoryFactory;
use Doctrine\Persistence\ObjectManager;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\EvenementsRepository;
use Symfony\Contracts\Translation\TranslatorInterface;
use Dompdf\Options;
use  Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
class EvenementsController extends AbstractController
{   /**
 * @Route("/ShowEvent", name="display_Event")
 */
    public function AfficherJSON(NormalizerInterface $Normalizer)
    {
        $us= $this->getUser();
        $en=$this->getDoctrine()->getManager()->getRepository(Evenements::class)->findAll();
        $evenement = $this->getDoctrine()->getManager()->getRepository(Evenements::class)->findAll();
        $jsonContent = $Normalizer->normalize($evenement,'json',['groups'=>'post:read']);


        return new Response(json_encode($jsonContent));;
    }



    /**
     * @Route("/ShowevenementS", name="ShowevenementS")
     */
    public function ShowevenementS(NormalizerInterface $Normalizer,Request $request)
    {


        $evenement = $this->getDoctrine()->getManager()->getRepository(Evenements::class)->find($request->get('id'));
        $jsonContent = $Normalizer->normalize($evenement,'json',['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }





    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route ("/Addevent",name="ajouterevent2")
     */
    function AjouterJSON(NormalizerInterface $Normalizer,Request $request)
    {




        $em=$this->getDoctrine()->getManager();
        $evenement=new Evenements();

        $evenement->setNom($request->get('nom'));
        $evenement->setDate($request->get('date'));
        $evenement->setImageName($request->get('imageName'));
        $evenement->setNbrplace($request->get('nbrplace'));
        $evenement->setDatef($request->get('datef'));
        $evenement->setLieu($request->get('Lieu'));
        $em->persist($evenement);
        $em->flush();
        $jsonContent = $Normalizer->normalize($evenement,'json',['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));;



        return $this->render('back/evenements.html.twig');
    }




    /**
     * @Route("/evenements", name="evenements")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(): Response
    {
        return $this->render('evenements/acceuil.htmltwig', [
            'controller_name' => 'EvenementsController',
        ]);
    }

    /**
     * @param EvenementsRepository $repository
     * @return \Symfony\Component\HttpFoundation\Response
     * @IsGranted("ROLE_ADMIN")
     * @Route("/listp", name="listp")
     */
    public function Affichagep(EvenementsRepository $repository)
    {
        $user = $this->getUser();

        //$en=$this->getDoctrine()->getManager()->getRepository(Evenements::class)->findAll();
        // var_dump($en);
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $en = $repository->findAll();

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('back/listp.html.twig ',
            ['formations' => $en, 'us' => $user]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => true
        ]);

    }


    /**
     * @param EvenementsRepository $repository
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/evenementf", name="affiche")
     */
    public function Affichage(EvenementsRepository $repository)
    {
        $sp=$this->getDoctrine()->getManager()->getRepository(Sponsors::class)->findAll();

        //$en=$this->getDoctrine()->getManager()->getRepository(Evenements::class)->findAll();
        // var_dump($en);
        $en = $repository->findAll();
        return $this->render('front/evenements.html.twig ',
            ['events' => $en,'sponsor' => $sp]);
    }

    /**
     * @Route("/supprimer{id}", name="supprimer")
     */
    public function supprimer(Evenements $event, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($event);
        $entityManager->flush();
        $this->addFlash(
            'info',
            'Deleted successfuly'
        );
        return $this->redirectToRoute('evenements');
    }


    /**
     * @Route("/evenement", name="evenements")
     * @IsGranted("ROLE_ADMIN")
     */
    public function AjouterEvenement(Request $request)
    {
        $user = $this->getUser();
        $en = $this->getDoctrine()->getManager()->getRepository(Evenements::class)->findAll();
        $evenement = new Evenements();
        $form = $this->createForm(EvenementsType::class, $evenement);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($evenement);
            $em->flush();
            $this->addFlash(
                'info',
                'Added Successfuly'
            );
            return $this->redirectToRoute('evenements');
        }
        return $this->render('back/evenements.html.twig', ['form' => $form->createView(), 'formations' => $en, 'us' => $user
        ]);
    }



    /**
     * @param Request $request
     * @Route("/ModifierEvenements/{id}",name="modifierevenement")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    function modifier(EvenementsRepository $repository,$id,Request $request,TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $sponsors = $repository->find($id);
        $form = $this->createForm(EvenementsType::class, $sponsors);
        $en = $this->getDoctrine()->getManager()->getRepository(Evenements::class)->findAll();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $message = $translator->trans('Event modified successfuly');
            $this->addFlash('message',$message);
            return $this->redirectToRoute('evenements');
        }
        return $this->render('back/evenements.html.twig',
            [
                'form' => $form->createView(), 'formations' => $en, 'us' => $user
            ]
        );

    }

    /**
     * @Route("/post/{id}/likes", name="post_likess")
     * @param Evenements $post
     * @param EntityManagerInterface $entityManager
     * @param PostLikeRepository $likeRepo
     * @return Response
     */
    public function likes(Evenements $post,EntityManagerInterface $entityManager,PostLikesRepository $likeRepo):Response{


        $user = $this->getUser();
        if (!$user) {
            return $this->json(['code' => 403, 'message' => 'Vous devez être connecté !'], 403);
        }

        if ($post->isLikedByUser($user)) {
            $likes = $likeRepo->findOneBy(['post' => $post, 'user' => $user]);
            $entityManager->remove($likes);
            $entityManager->flush();
            return $this->json(['code' => 200,'message' =>'Like supprime', 'likes' => $likeRepo->count(['post'=> $post])], 200);
        }
        $likes = new PostLikes();
        $likes->setPost($post)
            ->setUser($user);

        $entityManager->persist($likes);
        $entityManager->flush();

        return $this->json(['code' =>200,'message' =>'Like bien ajoute',
            'likes' => $likeRepo->count(['post'=> $post])
        ],200);
    }




}