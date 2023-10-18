<?php

namespace App\Controller;

use App\Entity\Livraison;
use App\Form\LivraisonType;
use App\Repository\LivraisonRepository;
use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LivController extends AbstractController
{
    /**
     * @Route("/admin/livraison/affiche", name="affichelivraison")
     */
    public function afficher(LivraisonRepository $repository): Response
    {
        return $this->render('liv/index.html.twig', [
            'livraisons' => $repository->findAll(),'element'=>'afficherliv',
        ]);
    }
    /**
     * @Route("/admin/livraison/update/{id}", name="updatelivraison")
     */
    public function modifierCommande(Livraison $livraison,Request $request): Response
    {
        $form=$this->createForm(LivraisonType::class,$livraison);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('affichelivraison');


        }
        return $this->render('liv/update.html.twig',['element'=>'modifliv','form'=>$form->createView()]);

    }
    /**
     * @Route("/admin/livraison/remove/{id}", name="deletelivraison")
     */
    public function deleteCommande(Livraison $livraison): Response
    {
        $em= $this->getDoctrine()->getManager();
        $em->remove($livraison);
        $em->flush();
        return $this->redirectToRoute('affichelivraison');
    }
    /**
     * @Route("/livraison/new", name="new_livraison")
     */
    public function newLivraison(Request $request,ProduitRepository $repository,UserRepository $userRepository): Response
    {
        $panier = $request->getSession()->get('panier', []);
        $tot = 0;

        $livraison = new Livraison();


        $livraison->setDateCreation(new \DateTime());
        $livraison->setState('waiting');

        $user = $userRepository->find(1);

        foreach ($panier as $item => $quantity) {

            $prod = $repository->find($item);
            $tot = $tot + ($quantity*$prod->getPrix());






        }

        $livraison->setDestination($_POST['dest']);
        $livraison->setPrixTotal($tot);
        $livraison->setUser($user);
        $em=$this->getDoctrine()->getManager();
        $em->persist($livraison);
        $em->flush();

        $request->getSession()->set('panier',[]);
        return $this->redirectToRoute('app_produit');
    }
}
