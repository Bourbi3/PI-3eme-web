<?php

namespace App\Controller;

use App\Entity\Produit;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\isEmpty;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier/add/{id}", name="app_panier")
     */
    public function index($id,Request $request): Response
    {
        $panier = $request->getSession()->get('panier',[]);
        if(!empty($panier[$id])){
            $panier[$id]++;

        }
        else{
            $panier[$id]=1;


        }
        $request->getSession()->set('panier',$panier);
        //return $this->redirectToRoute('app_produit');
        return $this->redirectToRoute('app_produit');



    }
    /**
     * @Route("/panier/remove/{id}", name="remove_cart")
     */
    public function removeCart($id,Request $request): Response
    {
        $panier = $request->getSession()->get('panier',[]);
        unset($panier[$id]);
        $request->getSession()->set('panier',$panier);
        //return $this->redirectToRoute('app_produit');
        return $this->redirectToRoute('full_cart');



    }

    /**
     * @Route("/addprod", name="add_cart")
     */
    public function addProductToCart(Request $request){
        $panier = $request->getSession()->get('panier',[]);
        $id = $_GET['id'];
        if(!empty($panier[$id])){
            $panier[$id]++;

        }
        else{
            $panier[$id]=1;


        }
        $quan =0;
        foreach ($panier as $prod=>$quantity){
            $quan += $quantity;


        }
        $request->getSession()->set('panier',$panier);
        //return $this->redirectToRoute('app_produit');



        return new JsonResponse(['quantity'=>$quan]);
    }
}
