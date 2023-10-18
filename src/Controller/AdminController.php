<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Form\CategorieFormType;
use App\Form\ProduitFormType;
use App\Form\ReponseType;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use App\Repository\ReclamationRepository;
use App\Repository\ReponseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="home")
     */
    public function index(): Response
    {

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    /**
     * @Route("/admin/categorie/affiche", name="affichecat")
     */
    public function catAffiche(CategorieRepository $repository): Response
    {
        return $this->render('admin/affichecat.html.twig', [
            'cat' => $repository->findAll(),'element'=>'afficher'
        ]);
    }
    /**
     * @Route("/admin/reclamations/affiche", name="afficherec")
     */
    public function recAffiche(ReclamationRepository $repository): Response
    {
        return $this->render('admin/reclamations.html.twig', [
            'rec' => $repository->findAll(),'element'=>'afficherrec'
        ]);
    }
    /**
     * @Route("/admin/reclamation/respond/{id}", name="respond")
     */
    public function respond(Reclamation $reclamation,Request $request,ReponseRepository $repository): Response
    {

        if($repository->findOneBy(['reclamation'=>$reclamation->getId()])==null){
            $response = new Reponse();
            $element = 'none';
        }
        else{
            $response=$repository->findOneBy(['reclamation'=>$reclamation->getId()]);
            $element= 'notnull';
        }


        $form=$this->createForm(ReponseType::class,$response);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $response->setReclamation($reclamation);
            $em = $this->getDoctrine()->getManager();

            if($element=='none'){
                $em->persist($response);

            }

            $em->flush();
            return $this->redirectToRoute('afficherec');



        }

        return $this->render('admin/addResponse.html.twig', [
            'controller_name' => 'AdminController','form'=>$form->createView(),'element'=>$element
        ]);
    }


    /**
     * @Route("/admin/produit/affiche", name="afficheprod")
     */
    public function prodAffiche(ProduitRepository $repository): Response
    {
        return $this->render('admin/afficherprod.html.twig', [
            'produit' => $repository->findAll(),'element'=>'afficher_prod'
        ]);
    }
    /**
     * @Route("/admin/categorie/supprimer/{id}", name="deletecat")
     */
    public function catdelete(CategorieRepository $repository,Categorie $categorie): Response
    {
        $em = $this->getDoctrine()->getManager();
        foreach ($categorie->getProduits() as $prod){
            $em->remove($prod);

        }
        $em->remove($categorie);
        $em->flush();

        return $this->redirectToRoute('affichecat');
    }
    /**
     * @Route("/admin/produit/supprimer/{id}", name="deleteprod")
     */
    public function proddelete(CategorieRepository $repository,Produit $produit): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($produit);
        $em->flush();
        return $this->redirectToRoute('afficheprod');
    }
    /**
     * @Route("/admin/categorie/update/{id}", name="updatecat")
     */
    public function catupdate(CategorieRepository $repository,Categorie $categorie,Request $request): Response
    {

        $form=$this->createForm(CategorieFormType::class,$categorie);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();

            $em->flush();
            return $this->redirectToRoute('affichecat');


        }

        return $this->render('admin/ajouterCategorie.html.twig', [
            'controller_name' => 'AdminController','form'=>$form->createView(),'element'=>'modifier'
        ]);
    }
    /**
     * @Route("/admin/produit/update/{id}", name="updateprod")
     */
    public function produpdate(ProduitRepository $repository,Produit $produit,Request $request): Response
    {

        $form=$this->createForm(ProduitFormType::class,$produit);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //$prod->setImage($form['image']->getData());
            $em = $this->getDoctrine()->getManager();
            $image = $produit->getImage();
            if (!str_contains($image,'front/' )) {
                $image="front/images/".$produit->getImage();

            }
            $produit->setImage($image);
            $em->persist($produit);
            $em->flush();
            return $this->redirectToRoute('afficheprod');


        }

        return $this->render('admin/ajouterProduit.html.twig', [
            'controller_name' => 'AdminController','form'=>$form->createView(),'element'=>'modifier'
        ]);
    }
    /**
     * @Route("/admin/ajouterCategorie", name="ajoutercat")
     */
    public function ajouterCategorie(Request $request): Response
    {
        $cat = new Categorie();
        $form=$this->createForm(CategorieFormType::class,$cat);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->persist($cat);
            $em->flush();
           return $this->redirectToRoute('affichecat');



        }

        return $this->render('admin/ajouterCategorie.html.twig', [
            'controller_name' => 'AdminController','form'=>$form->createView(),'element'=>'ajouter'
        ]);
    }
    /**
     * @Route("/admin/ajouterProduit", name="ajouterprod")
     */
    public function ajouterProduit(Request $request): Response
    {
        $prod = new Produit();
        $form=$this->createForm(ProduitFormType::class,$prod);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

          //$prod->setImage($form['image']->getData());
            $em = $this->getDoctrine()->getManager();
            $image="front/images/".$prod->getImage();
            $prod->setImage($image);
            $em->persist($prod);
            $em->flush();
            return $this->redirectToRoute('afficheprod');


        }

        return $this->render('admin/ajouterProduit.html.twig', [
            'controller_name' => 'AdminController','form'=>$form->createView(),'element'=>'ajouterprod'
        ]);


    }
    /**
     * @Route("/admin/produit/stats", name="stats")
     */
    public function getStats(CategorieRepository $categorieRepository){
        $cats = [];
        $produitscount=[];
        $all = $categorieRepository->findAll();
        foreach ($all as $cat){
            $cats[] = $cat->getName();
            $produitscount[]=count($cat->getProduits());

        }

        return $this->render('admin/stats.html.twig',['element'=>'stats','names'=>json_encode($cats),'number'=>json_encode($produitscount)]);
    }
}
