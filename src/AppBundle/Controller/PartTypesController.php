<?php

// src/AppBundle/Controller/PartTypesController.php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\PartType;
use AppBundle\Form\Parts\PartType as FPartType;

class PartTypesController extends Controller {

    public function addAction(Request $request) {
        // Generates the form
        $partType = new PartType();
        $form = $this->createForm(new FPartType(), $partType);
        $form->handleRequest($request);

        // If form is posted and valid, then saves
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($partType);
            $em->flush();
        }

        // Renders the add part screen
        $html = $this->container->get('templating')->render(
                'parttypes/add.html.twig', array('form' => $form->createView())
        );
        return new Response($html);
    }

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
        }

        $html = $this->container->get('templating')->render(
                'parttypes/manage.html.twig', array('form'=>$form->createView())
        );
        return new Response($html);
    }

}
