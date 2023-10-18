<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use App\Repository\ReponseRepository;
use App\Repository\UserRepository;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReclamationController extends AbstractController
{
    /**
     * @Route("/reclamation", name="app_reclamation")
     */
    public function index(Request $request,ReclamationRepository $repository,UserRepository $userRepository): Response
    {
        $panier = $request->getSession()->get('panier', []);
        $tot = 0;

        foreach ($panier as $item => $quantity) {
            $tot = $tot + $quantity;


        }
        $reclamations = $repository->findAll();
            $reclamation = new Reclamation();
            $form = $this->createForm(ReclamationType::class,$reclamation);
            $form->handleRequest($request);
            $res = new Response();
            $res->setContent('hello from admin');
            if($form->isSubmitted() && $form->isValid()){
                if($_POST['g-recaptcha-response']==""){
                    $error='you must validate the capcha test';
                    return $this->render('reclamation/index.html.twig', [
                        'controller_name' => 'ReclamationController','reclamation'=>$reclamations,'form'=>$form->createView(),'res'=>$res,'error'=>$error,'total'=>$tot
                    ]);

                }
                $em = $this->getDoctrine()->getManager();

                $user = $userRepository->find(1);
                $reclamation->setUser($user);
                $em->persist($reclamation);
                $em->flush();
                return $this->redirectToRoute('app_produit');

            }
        return $this->render('reclamation/index.html.twig', [
            'controller_name' => 'ReclamationController','reclamation'=>$reclamations,'form'=>$form->createView(),'res'=>$res,'total'=>$tot
        ]);

    }


    /**
     * @Route("/getresponse/{id}", name="getres")
     */
    public function getResponse($id,ReponseRepository $repository): Response
    {

       $response = $repository->findOneBy(['reclamation'=>intval($id)]);

        return new JsonResponse([
            'id'=>$id,
            'content' => $this->renderView('reclamation/response.html.twig',['res'=>$response])

        ]);

    }
    /**
     * @Route("/rec/delete/{id}", name="supprimerrec")
     */
    public function delete(Reclamation $reclamation,ReponseRepository $repository): Response
    {
      $em = $this->getDoctrine()->getManager();
        $response = $repository->findOneBy(['reclamation'=>intval($reclamation->getId())]);
        if($response!=null){
            $em->remove($response);
            $em->flush();
        }
        $em->remove($reclamation);
        $em->flush();


        return $this->redirectToRoute('app_reclamation');


    }
    /**
     * @Route("/reclamation/chercher", name="searchrec")
     */
    public function search(ReclamationRepository $reclamationRepository){
        $reclamations = $reclamationRepository->searchReclamation($_GET['name']);

         return new JsonResponse(['content'=>$this->renderView('admin/recFilter.html.twig',['rec'=>$reclamations])]);
      //  /reclamation/chercher
    }

}
