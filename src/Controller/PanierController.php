<?php

namespace App\Controller;

use App\Entity\Coupon;
use App\Entity\LigneCommande;
use App\Entity\Utilisateur;
use App\Repository\CommandeRepository;
use App\Repository\CouponRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProduitsRepository ;
use Symfony\Component\HttpFoundation\Session\SessionInterface ;
use App\Entity\Produits;
use App\Entity\Commande ;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PanierController extends AbstractController
{
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/AjoutComm",name="AjoutComm")
     */
    public function AjoutComm(Request $request,NormalizerInterface  $Normalizer) : Response
    {$total = 0;
        $commande = new Commande();
        $commande->setDateCommande(new \DateTime());
        $commande->setStatue("Non Paye");
        $en = $this->getDoctrine()->getManager();

        $en->persist($commande);
        $en->flush();
        foreach ($request->query->all() as $key => $value) {
            if ($key == "user") {
                $user =  $this->getDoctrine()->getManager()->getRepository(Utilisateur::class)->find($value);
                $commande->setUtilisateur($user);


            }else {
                $ligne = new LigneCommande();
                /** @var Produits $prod */
                $prod =  $this->getDoctrine()->getManager()->getRepository(Produits::class)->find($key);
                $ligne->setQuantite($value);
$ligne->setPrix($prod->getPrix()*$value);                
$ligne->setProduit($prod);
                
                $ligne->setCommande($commande);
                $en = $this->getDoctrine()->getManager();

                $en->persist($ligne);
                $en->flush();

                $total += $prod->getPrix()*$value ;
            }

            }
        $commande->setPrix($total);
        $en->persist($commande);
        $en->flush();
        $jsonContent = $Normalizer->normalize($commande, 'json', ['groups' => 'post:read']);
            return new Response(json_encode($jsonContent));


    }
        /**
     * @Route("/pay",name="payement")
     */
    public function payement(Request $request,CommandeRepository $rp) : Response{
        if ($request->isMethod('POST')) {

            foreach ($request->query->all() as $key => $value) {
                if ($key !== "amount") {

                    $commande = $rp->find(intval ($value));
                    $commande->setStatue("Payé");
                    $this->getDoctrine()->getManager()->flush();
                } else {
                      $stripe = new \Stripe\StripeClient(
                           'sk_test_51IXl9nAyyifkJ2GTw02VQPccPVPzbU7UW382UezlP4Npm0ajBpy9eJMhiFk3PHdfvO7Co06fR2dzmXlqMei3CqPC00ZksblkBB'
                       );
                       $stripe->charges->create([
                           'amount' => intval ($value),
                           'currency' => 'eur',
                           'source' => $request->request->get('stripeToken'),
                           'description' => 'My First Test Charge (created for API docs)',
                       ]);
                       return new RedirectResponse($this->generateUrl('ajouterlivraisons'));
                   }
                }
            }

                return $this->render('front/payement.html.twig');
    }
    /**
     * @Route("/panier", name="panier")
     */

    public function panier(Request $request, ProduitsRepository $pr): Response
    {
        //  $this->session->set('Pid', [1,2]);
        $pids = $this->session->get('Pid', []);
        $produits = $pr->findBy(['id' => $pids]);
        $total = 0;
        foreach ($produits as $produit) {
            $total += $produit->getPrix();
        }
        return $this->render('front/panier.html.twig', ['produits' => $produits, 'total' => $total]);
    }
    /**
     * @Route("/topaye/{id}", name="topaye")
     */
    public function topaye($id,CommandeRepository $rp): Response
    {
        $com = $rp->find($id);
        $com->setStatue("Paye");
        $this->getDoctrine()->getManager()->flush();
        return $this->json(['code' => 200], 200);
    }
    /**
     * @Route("/ajoutpanier/{id}", name="panieraj")
     */
    public function ajoutpanier($id): Response
    {

        $produits = $this->session->get('Pid', []);
        $produits = \array_diff($produits, [$id]);
        $produits[]=$id ;
        $this->session->set('Pid', $produits);
        return $this->json(['code' => 200, 'pid' => $produits], 200);
    }
    /**
     * @Route("/erreur", name="erreur")
     */
    public function erreur(Request $request): Response
    {
        $er =   $request->get('er');
        return $this->render('front/erreur.html.twig', ['erreur' => $er]);
    }
        /**
     * @Route("/panierdel/{id}", name="panierdel")
     */
    public function deleteprod($id): Response
    {

        try {
            $produits = $this->session->get('Pid', []);
            $produits = \array_diff($produits, [$id]);
            $this->session->set('Pid', $produits);
        } catch (\Exception $e) {
            return $this->json(['code' => 500, 'Exception' => $e], 500);
        }
        return $this->json(['code' => 200, 'pid' => $produits], 200);
    }

    /**
     * @Route ("/ajoutcom", name="ajoutcom")
     */
    public function ajoutcom(ProduitsRepository $produitRepository, Request $request): Response
    {$total = 0 ;
    $idC=[];
        $user = $this->getUser();
    if ($user) {
        if ($user->isVerified()) {
            $idP = $this->session->get('Pid', []);
            $parametersAsArray = [];
            if ($content = $request->getContent()) {
                $parametersAsArray = json_decode($content, true);
            }
            if ($idP != []) {
                $Utilisateur = $this->getUser();

                $commande = new Commande();
                $commande->setDateCommande(new \DateTime());
                $commande->setStatue("Non Paye");
                $en = $this->getDoctrine()->getManager();
                $commande->setUtilisateur($Utilisateur);

                $en->persist($commande);
                $en->flush();
                if(isset ($parametersAsArray['reduction'])) {
                    $vrai = 1 - ($parametersAsArray['reduction'] / 100);
                }
                else {
                $vrai = 1 ;
                }

                $i = 0;
                $produits = $produitRepository->findBy(['id' => $idP]);

                foreach ($produits as $prod) {
                    if ($prod->getQuantite()>= $parametersAsArray['qty'][$i] ) {
                        $prod->setQuantite($prod->getQuantite()- $parametersAsArray['qty'][$i]);
                        $ligne = new LigneCommande();
                        $ligne->setPrix($prod->getPrix() * $parametersAsArray['qty'][$i]*$vrai);
                        $ligne->setProduit($prod);
                        $ligne->setCommande($commande);
                        $ligne->setQuantite($parametersAsArray['qty'][$i]);
                        $en = $this->getDoctrine()->getManager();
                        $en->persist($ligne);
                        $en->flush();

                        $total += ($prod->getPrix() * $parametersAsArray['qty'][$i]);
                        $en = $this->getDoctrine()->getManager();

                        $i = $i + 1;
                    }else {
                        return $this->json(['code' => 200, 'link' => "http://127.0.0.1:8000/erreur?er=cette quantite n'existe pas"], 200);
                    }
                }
                $idC[] = $commande->getId();
                $idC['amount']=$total ;
                $this->session->set('Pid', []);
                return $this->json(['code' => 200, 'link' => "http://127.0.0.1:8000/pay?".http_build_query($idC)], 200);
            }
            return $this->json(['code' => 200, 'link' => "http://127.0.0.1:8000/panier"], 200);
        } else {
            return $this->json(['code' => 200, 'link' => "http://127.0.0.1:8000/erreur?er=verfier votre compte par mail stp"], 200);
        }
    }else {
        return $this->json(['code' => 200, 'link' => "http://127.0.0.1:8000/loginf"], 200);
    }
}

/**
 * @Route("/getpour",name="getpour")
 */
public function getpour(Request $request,CouponRepository $couponRepository){
  /** @var Coupon $coup */
    $coup = $couponRepository->findOneBy(['code'=>$request->get('code')]) ;
    if ($coup) {
        return $this->json(['pourcentage' => $coup->getPourcentage()],200);
    }else {
        return $this->json([],404);
    }
}
}
