<?php

// src/AppBundle/Controller/PartTypesController.php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\PartType;
use AppBundle\Form\Parts\PartType as FPartType;

class PartTypesController extends Controller {
    
    /**
     * Used to store notice messages to be displayed at the top of the 
     * manage/edit windows after an ection has been carried out.
     */
    private $displayMessage= array(
        'class'=>'alert-success',
        'value'=>''
    );

    public function getAction() {

        // Gets types
        $repository = $this->getDoctrine()
                ->getRepository('AppBundle:PartType');
        $query = $repository->createQueryBuilder('pt')
                ->getQuery();
        $locations = $query->getResult();

        //Outputs to browser
        $output = [];
        foreach ($locations as $l) {
            $output[] = [
                'id' => $l->getId(),
                'name' => $l->getName(),
                'description' => $l->getDescription()
            ];
        }
        $response = new JsonResponse();
        $response->setData(
                $output
        );
        return $response;
    }

    public function manageAction(Request $request) {
        $partType = new PartType();
        $form = $this->createForm(new FPartType(), $partType);
        $form->handleRequest($request);
        
        // If form is posted and valid, then saves
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($partType);
            $em->flush();
            $this->displayMessage['value'] = 'Successfuly added part type';
            $form = $this->createForm(new FPartType(), $partType);
        }

        $html = $this->container->get('templating')->render(
            'parttypes/manage.html.twig',
            array(
                'form'=>$form->createView(),
                'displayMessage'=>$this->displayMessage
            )
        );
        return new Response($html);
    }

}
