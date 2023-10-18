<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

 use App\Entity\Reclamation;
 use App\Entity\User;
 use App\Repository\ReclamationRepository;
 use App\Repository\UserRepository;
 use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 use Symfony\Component\HttpFoundation\JsonResponse;
 use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpFoundation\Response;

class MobileController extends AbstractController
{
    /**
     * @Route("/mobile", name="app_mobile")
     */
    public function index(): Response
    {
        return $this->render('mobile/index.html.twig', [
            'controller_name' => 'MobileController',
        ]);
    }

    /**
      * @Route("/deleteReclamation/{id}", name="delete_reclamation")
      */

      public function deleteReclamationAction(Request $request ,NormalizerInterface $Normalizer,$id) {
        $id = $request->get("id");

        $em = $this->getDoctrine()->getManager();
        $reclamation = $em->getRepository(Reclamation::class)->find($id);
        if($reclamation!=null ) {
            $em->remove($reclamation);
            $em->flush();

          $jsonContent = $Normalizer->normalize($reclamation ,'json',['groups'=>'post:read']);
          return new Response("Reclamation bien supprimé".json_encode($jsonContent));

        }
        return new JsonResponse("id reclamation invalide.");


    }


    /**
      * @Route("/displayReclamations", name="display_reclamations")
      */
      public function allRecAction( NormalizerInterface $Normalizer )
      {
 
          $repository = $this->getDoctrine()->getRepository(Reclamation::class);
          $reclamation = $repository->findAll();
          $jsonContent = $Normalizer->normalize($reclamation ,'json',['groups'=>'post:read']);
          return new Response(json_encode($jsonContent));
          
 
      }

       /**
      * @Route("/addReclamation", name="add_reclamation")
      */

     public function ajouterReclamationAction(Request $request , NormalizerInterface $Normalizer,UserRepository $userRepository)
     {
         $em = $this->getDoctrine()->getManager();
         $reclamation = new Reclamation();

         //$id_user=$request->get('x');
         $user = $userRepository->find(1);
         $reclamation->setUser($user);
         $reclamation->setNom($request->get('nom'));
         $reclamation->setMsg($request->get('msg'));
        
         $em->persist($reclamation);
         $em->flush();

         $jsonContent = $Normalizer->normalize($reclamation ,'json',['groups'=>'post:read']);
         return new Response(json_encode($jsonContent));
        

     }


      /**
      * @Route("/updateReclamation/{id}", name="update_reclamation")
      */

      public function updateReclamationAction(Request $request , NormalizerInterface $Normalizer,$id)
      {
          $em = $this->getDoctrine()->getManager();
          $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->find($id);
 
          $reclamation->setNom($request->get('nom'));
          $reclamation->setMsg($request->get('msg'));
         
          $em->flush();
 
          $jsonContent = $Normalizer->normalize($reclamation ,'json',['groups'=>'post:read']);
          return new Response(json_encode($jsonContent));
         
 
      }


}
