<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Location;
use AppBundle\Entity\LocationNote;
use AppBundle\Form\Locations\Location as FLocation;
use AppBundle\Form\Locations\LocationNote as FLocationNote;
use DateTime;

/**
 * Locations
 * Parts get added to a location. This file controls the management of locations
 * in the system.
 */
class LocationsController extends Controller {
    
    /**
     * Used to store notice messages to be displayed at the top of the 
     * manage/edit windows after an ection has been carried out.
     */
    private $displayMessage= array(
        'class'=>'alert-success',
        'value'=>''
    );
    
    /**
     * Form Add/Update
     * @param Request $request
     * @param Location $location 
     * @return type
     */
    private function formUpdate(Request $request, Location $location){
        $form = $this->createForm(new FLocation(), $location);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($location);
            $em->flush();
            $this->displayMessage['value'] = 'Successfuly updated location.';
            $this->displayMessage['class'] = 'alert-info';
        }
        return $form;
    }
    
    private function formAdd(Request $request, Location $location){
        $form = $this->createForm(new FLocation(), $location);
        $form->handleRequest($request);
        
        // If the form is valid then save it and return a blank form otherwise
        // return the form with any errors in.
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($location);
            $em->flush();
            $this->displayMessage['value'] = 'Successfuly added location.';
            return $this->createForm(new FLocation(), new Location());
        } else{
            return $form;
        }
    }
    
    private function formLNAdd($locationId,Request $request, LocationNote $record){
        $form = $this->createForm(new FLocationNote(), $record);
        $form->handleRequest($request);
        
        // If the form is valid then save it and return a blank form otherwise
        // return the form with any errors in.
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $createdDate = new DateTime('Europe/London');
            $record->setAdded($createdDate);
            $record->setAddedBy($this->getUser());
            $record->setLocationId($locationId);
            $em->persist($record);
            $em->flush();
            $this->displayMessage['value'] = 'Successfuly added location note.';
            $this->displayMessage['class'] = 'alert-info';
            return $this->createForm(new FLocationNote(), new LocationNote());
        } else{
            return $form;
        }
    }

    /**
     * Get Manage Screen
     * This will return the manage locations screen.
     * @param Request $request
     * @return Response The manage locations screen
     */
    public function manageAction(Request $request) {      
        $form = $this->formAdd($request, new Location());
        $html = $this->container->get('templating')->render(
            'locations/manage.html.twig',
            array(
                'form' => $form->createView(),
                'displayMessage' => $this->displayMessage
            )
        );
        return new Response($html);
    }

    
    /**
     * Get location list
     * Finds a list of locations in the database which are not marked as 
     * deleted. The items found are returned based on the supplied limit and
     * offset. This funcation may not return all the items from the table if
     * there are more than the total number. The total number of items is also
     * returned from the system.
     * @return JsonResponse containing up to 10 locations requested from the
     * system
     */
    public function getAction(Request $request) {
        $limit = 10;
        $offset = 0;
        if($request->query->get('limit') && $request->query->get('offset')){
            $limit = $request->query->get('limit');
            $offset = $request->query->get('offset');
        }
        $searchTerm = $request->query->get('search');
        $em = $this->getDoctrine()->getManager();
        $response = new JsonResponse();
        $response->setData(
            Location::search($em,$searchTerm,$limit,$offset)
        );
        return $response;
    }
    
    public function getNotesAction($locationId,Request $request) {
        $limit = 10;
        $offset = 0;
        if($request->query->get('limit') && $request->query->get('offset')){
            $limit = $request->query->get('limit');
            $offset = $request->query->get('offset');
        }
        $searchTerm = $request->query->get('search');
        $em = $this->getDoctrine()->getManager();
        $response = new JsonResponse();
        $response->setData(
            LocationNote::search($em,$locationId,$searchTerm,$limit,$offset)
        );
        return $response;
    }

    /**
     * Edit location screen
     * @param int $id id of the location to edit
     * @param Request $request containing the updated location information
     * @return Response
     */
    public function editAction($id, Request $request) {
        $location = $this->getDoctrine()
        ->getRepository('AppBundle:Location')
        ->find($id);
        $form = $this->formUpdate($request, $location);
        $lNForm = $this->formLNAdd($id,$request, new LocationNote());
        $html = $this->container->get('templating')->render(
            'locations/edit.html.twig',
            array(
                'formLocation' => $form->createView(),
                'formLocationNote' => $lNForm->createView(),
                'displayMessage' => $this->displayMessage
            )
        );
        return new Response($html);
    }

}
