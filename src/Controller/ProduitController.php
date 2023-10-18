<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Livraison;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\isEmpty;

class ProduitController extends AbstractController
{
    /**
     * @Route("/produit", name="app_produit")
     */
    public function index(ProduitRepository $produitRepository,CategorieRepository $repository,Request $request): Response
    {
        $panier = $request->getSession()->get('panier', []);
        $tot = 0;

            foreach ($panier as $item => $quantity) {
                $tot = $tot + $quantity;


            }


        return $this->render('produit/index.html.twig', [
            'controller_name' => 'ProduitController','categories'=>$repository->findAll(),'produits'=>$produitRepository->findAll(),'total'=>$tot
        ]);
    }
    /**
     * @Route("/cart", name="full_cart")
     */
    public function getFullCart(ProduitRepository $produitRepository,CategorieRepository $repository,Request $request): Response
    {
        $panier = $request->getSession()->get('panier', []);
        $tot = 0;
        $produits = [];

        foreach ($panier as $item => $quantity) {
            $tot = $tot + $quantity;
            $prod = $produitRepository->find($item);
            array_push($produits, $prod);



        }



        return $this->render('produit/cart.html.twig',['produits'=>$produits,'total'=>$tot,'cart'=>$panier]);
    }
    /**
     * @Route("/commande/new", name="new_commande")
     */
    public function newOrder(Request $request,ProduitRepository $repository,UserRepository $userRepository): Response
    {
        $panier = $request->getSession()->get('panier', []);
        $tot = 0;

        $commande = new Commande();

        $commande->setCreationDate(new \DateTime());
        $commande->setState('waiting');
        $user = $userRepository->find(1);

        foreach ($panier as $item => $quantity) {

            $prod = $repository->find($item);
            $tot = $tot + ($quantity*$prod->getPrix());
            $commande->addListprod($prod);




        }
        $commande->setUser($user);
        $commande->setPrixTotal($tot);

        $em = $this->getDoctrine()->getManager();
        $em->persist($commande);
        $em->flush();

        $request->getSession()->set('panier',[]);



        return $this->redirectToRoute('app_produit');
    }
    /**
     * @Route("/all", name="all")
     */
    public function getAllProducts(ProduitRepository $produitRepository,CategorieRepository $categorieRepository,Request $request){
        $panier = $request->getSession()->get('panier', []);
        $tot = 0;

        foreach ($panier as $item => $quantity) {
            $tot = $tot + $quantity;


        }
        if(isset($_GET['page'])){
            $page=$_GET['page'];

        }
        else{
            $page=1;

        }
        $produits= $produitRepository->getProductByCatName(-1,$page);

        return $this->render('produit/all.html.twig',['products'=>$produits,'cats'=>$categorieRepository->findAll(),'page'=>$page,'total'=>$tot]);

    }
    /**
     * @Route("/getProducts", name="getProd")
     */
    public function getProductsBycat(ProduitRepository $produitRepository){

       $id = $_GET['id'];

        $produits = $produitRepository->getProducts(intval($id));

        return new JsonResponse(['content'=>$this->renderView('produit/prodfilter.html.twig',['products'=>$produits])]);

       // return new JsonResponse(['get'=>$_GET]);
    }


}
