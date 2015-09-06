<?php

// src/AppBundle/Controller/LocationsController.php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Location;
use AppBundle\Entity\LocationNote;
use AppBundle\Form\Locations\Location as FLocation;
use AppBundle\Form\Locations\LocationDelete as FLocationDelete;

use DateTime;

class LocationsController extends Controller {
    
    /**
     * Form Add/Update
     * @param Request $request
     * @param Location $location 
     * @return type
     */
    private function formAddUpdate(Request $request, Location $location, $updateNote){
        $form = $this->createForm(new FLocation(), $location);
        $form->handleRequest($request);
        if ($form->isValid()) {
            // Save Location
            $em = $this->getDoctrine()->getManager();
            $em->persist($location);
            $em->flush();
//            // Add location note
//            $emln = $this->getDoctrine()->getManager();
//            $locationNote = new LocationNote();
//            $locationNote->setAddedBy($this->getUser()->getId());
//            $createdDate = new DateTime('Europe/London');
//            $locationNote->setAdded($createdDate);
//            $locationNote->setNotes($updateNote);
//            $locationNote->setLocationId($location->getId());
//            $emln->persist($locationNote);
//            $emln->flush();
        }
        return $form;
    }

    /**
     * Get Manage Screen
     * This will return the manage locations screen.
     * @param Request $request
     * @return Response The manage locations screen
     */
    public function manageAction(Request $request) {
        $form = $this->formAddUpdate($request, new Location(),"Location added");
        $locDeleteForm = $this->createForm(new FLocationDelete(), new Location());
        $html = $this->container->get('templating')->render(
            'locations/manage.html.twig',
            array(
                'form' => $form->createView(),
                'locDeleteForm'=>$locDeleteForm->createView()
            )
        );
        return new Response($html);
    }
    
    public function deleteAction(Request $request) {
//        $location = new Location();
//        $locDeleteForm = $this->createForm(new FLocationDelete(),$location);
//        $locDeleteForm->handleRequest($request);
//        
//        // If delete form is valid then do the delete / adding notes etc
//        if ($locDeleteForm->isValid()) {
//            $emln = $this->getDoctrine()->getManager();
//            $locationNote = new LocationNote();
//            $locationNote->setAddedBy($this->getUser()->getId());
//            $createdDate = new DateTime('Europe/London');
//            $locationNote->setAdded($createdDate);
//            $locationNote->setNotes('Location Deleted');
//            $locationNote->setLocationId($location->getIdDelete());
//            $emln->persist($locationNote);
//            $emln->flush();
//        }
//        
//        $response = new JsonResponse();
//        $response->setData();
//        return $response;
    }

    /**
     * Get location list
     * Finds a list of locations in the database which are not marked as 
     * deleted.
     * @return JsonResponse containing up to 10 locations requested from the
     * system
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
        $response->setData(
            array(
                'total'=>Location::getNoLocations($em),
                'rows'=>$query->getResult()
            )
        );
        return $response;
    }

    /**
     * Get edit location screen
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function editAction($id, Request $request) {
        $location = $this->getDoctrine()
        ->getRepository('AppBundle:Location')
        ->find($id);
        $form = $this->formAddUpdate($request, $location,"Location updated");
        $html = $this->container->get('templating')->render(
            'locations/edit.html.twig',
            array('formLocation' => $form->createView())
        );
        return new Response($html);
    }

}
