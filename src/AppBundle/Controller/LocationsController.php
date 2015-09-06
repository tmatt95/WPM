<?php

// src/AppBundle/Controller/LocationsController.php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Location;
use AppBundle\Form\Locations\Location as FLocation;

class LocationsController extends Controller {

    public function addAction(Request $request) {
        // Generates the form
        $location = new Location();
        $form = $this->createForm(new FLocation(), $location);
        $form->handleRequest($request);

        // If form is posted and valid, then saves
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($location);
            $em->flush();
        }

        $html = $this->container->get('templating')->render(
            'locations/manage.html.twig',
            array('form' => $form->createView())
        );
        return new Response($html);
    }

    public function manageAction() {
        $location = new Location();
        $form = $this->createForm(new FLocation(), $location);
        $html = $this->container->get('templating')->render(
            'locations/manage.html.twig',
            array('form' => $form->createView())
        );
        return new Response($html);
    }

    /**
     * Get location list
     * Finds a list of locations in the database which are not marked as 
     * deleted.
     * @return JsonResponse
     */
    public function getAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT l.id,
                l.name,
                CONCAT(SUBSTRING(l.description,1,50),\'...\') as description
            FROM AppBundle:Location l'
        );
        $query->setMaxResults($request->query->get('limit'));
        $query->setFirstResult($request->query->get('offset'));
        $response = new JsonResponse();
        $response->setData($query->getResult());
        return $response;
    }

    public function partsinAction() {
        $html = $this->container->get('templating')->render(
            'locations/partsin.html.twig',
            array()
        );
        return new Response($html);
    }

    public function editAction($id, Request $request) {
        // Generates the form
        $location = $this->getDoctrine()
        ->getRepository('AppBundle:Location')
        ->find($id);
        $form = $this->createForm(new FLocation(), $location); 
        
        $form->handleRequest($request);

        // If form is posted and valid, then saves
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($location);
            $em->flush();
        }
        
        $html = $this->container->get('templating')->render(
                'locations/edit.html.twig', array('formLocation' => $form->createView())
        );
        return new Response($html);
    }

}
