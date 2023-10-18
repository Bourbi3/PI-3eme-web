<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    /**
     * @Route("/admin/commande/affiche", name="affichecommande")
     */
    public function afficher(CommandeRepository $repository): Response
    {
        return $this->render('commande/index.html.twig', [
            'commandes' => $repository->findAll(),'element'=>'affichercom',
        ]);
    }
    /**
     * @Route("/admin/commande/update/{id}", name="updatecommande")
     */
    public function modifierCommande(Commande $commande,Request $request): Response
    {
       $form=$this->createForm(CommandeType::class,$commande);
       $form->handleRequest($request);
       if($form->isSubmitted() && $form->isValid()){
           $em = $this->getDoctrine()->getManager();
           $em->flush();
           return $this->redirectToRoute('affichecommande');


       }
       return $this->render('commande/update.html.twig',['element'=>'modifCommande','form'=>$form->createView()]);

    }
    /**
     * @Route("/admin/commande/remove/{id}", name="deletecommande")
     */
    public function deleteCommande(Commande $commande): Response
    {
        $em= $this->getDoctrine()->getManager();
        $em->remove($commande);
        $em->flush();
        return $this->redirectToRoute('affichecommande');
    }
    /**
     * @Route("/admin/commande/details/{id}", name="detailscommande")
     */
    public function detailsCommande(Commande $commande): Response
    {
      return $this->render('commande/details.html.twig',['c'=>$commande,'element'=>'details']);
    }
}
