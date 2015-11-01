<?php
/**
 * Locations
 * Parts get added to a location. This controls the management of locations
 * in the system.
 * 
 * PHP version 5.6
 * 
 * @category WPM
 * @package  Location
 * @author   Matthew Turner <tmatt95@gmail.com>
 * @license  http://opensource.org/licenses/GPL-3.0 GPL3
 * @version  GIT: <1.0.0>
 * @link     https://github.com/tmatt95/WPM/
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Location;
use AppBundle\Entity\LocationNote;
use AppBundle\Form\Locations\Location as FLocation;
use AppBundle\Entity\Part;
use AppBundle\Form\Locations\LocationNote as FLocationNote;
use AppBundle\Form\Locations\LocationDelete as FLocationDelete;
use DateTime;

/**
 * Locations
 * Parts get added to a location. This controls the management of locations
 * in the system.
 * 
 * PHP version 5.6
 * 
 * @category WPM
 * @package  Location
 * @author   Matthew Turner <tmatt95@gmail.com>
 * @license  http://opensource.org/licenses/GPL-3.0 GPL3
 * @version  Release: <1.0.0>
 * @link     https://github.com/tmatt95/WPM/
 */
class LocationsController extends Controller
{
    /**
     * Used to store notice messages to be displayed at the top of the 
     * manage/edit windows after an action has been carried out.
     */
    private $_displayMessage = array(
        'class' => 'alert-success',
        'showButton' => false,
        'buttonText' => 'Edit Location',
        'locationId' => null,
        'value' => '',
    );
    
    /**
     * Whether to redirect to the locations screen or not
     * @var boolean defaults to false 
     */
    private $_redirectToLocations = false;

    /**
     * Location Update
     * This will take the location form an try and update the database with it
     * if one is sent to it.
     * @param Request  $request  containing the POST data if sent
     * @param Location $location record to be updated
     * @return AppBundle\Form\Locations\Location
     */
    private function _formUpdate(Request $request, Location $location)
    {
        // Populates form if one sent to application
        $form = $this->createForm(new FLocation(), $location);
        $form->handleRequest($request);

        // If form is posted and valid, then saves
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($location);
            $em->flush();
            $this->_displayMessage['value'] = 'Successfuly updated location';
            $this->_displayMessage['class'] = 'alert-info';
        }
        return $form;
    }

    /**
     * Location Delete
     * Will move all parts out of a location and then remove the location from
     * the system.
     * @param Request  $request  may contain a location to remove
     * @param Location $location to remove
     * @return AppBundle\Form\Locations\LocationDelete
     */
    private function _formDelete(Request $request, Location $location)
    {
        // Populates form if one sent to application
        $form = $this->createForm(new FLocationDelete(), $location);
        $form->handleRequest($request);

        // If form is posted and valid, then saves
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            Part::moveAllOutLocation($em, $location->getId());
            LocationNote::deleteForLocation($em, $location->getId());
            $em->remove($location);
            $em->flush();
            $this->redirectToLocations = true;
        }
        return $form;
    }

    /**
     * Location Add
     * Will try and add a new location to the system if there is one to add.
     * @param Request  $request  containing the POST data if sent
     * @param Location $location record to be updated
     * @return AppBundle\Form\Locations\Location
     */
    private function _formAdd(Request $request, Location $location)
    {
        // Populates form if one sent to application
        $form = $this->createForm(new FLocation(), $location);
        $form->handleRequest($request);

        // If the form is valid then save it and return a blank form otherwise
        // return the form with any errors in.
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($location);
            $em->flush();
            $this->_displayMessage['value'] = 'Successfuly added location';
            $this->_displayMessage['showButton'] = true;
            $this->_displayMessage['locationId'] = $location->getId();
            return $this->createForm(new FLocation(), new Location());
        } else {
            return $form;
        }
    }

    /**
     * Location Note Add
     * @param type    $locationId location the note should link to
     * @param Request $request    may contain a location note to add
     * @return AppBundle\Form\Locations\LocationNote
     */
    private function _formLNAdd($locationId, Request $request)
    {
        // Populates form if one sent to application
        $record = new LocationNote();
        $form = $this->createForm(new FLocationNote(), $record);
        $form->handleRequest($request);

        // Adds server side data
        $createdDate = new DateTime('Europe/London');
        $record->setAdded($createdDate);
        $record->setAddedBy($this->getUser());
        $record->setLocationId($locationId);

        // If the form is valid then save it and return a blank form otherwise
        // return the form with any errors in.
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($record);
            $em->flush();
            $this->_displayMessage['value'] = 'Successfuly added location note.';
            $this->_displayMessage['class'] = 'alert-info';
            return $this->createForm(new FLocationNote(), new LocationNote());
        } else {
            return $form;
        }
    }

    /**
     * Manage Locations
     * The manage locations screen.
     * @param Request $request Could contain a new location
     * @return HTML The manage locations screen
     */
    public function manageAction(Request $request)
    {
        $form = $this->_formAdd($request, new Location());
        $html = $this->container->get('templating')->render(
            'locations/manage.html.twig',
            array(
                'form' => $form->createView(),
                'displayMessage' => $this->_displayMessage,
            )
        );
        return new Response($html);
    }

    /**
     * Get Location List
     * Finds a list of locations in the database which are not marked as 
     * deleted. The items found are returned based on the supplied limit and
     * offset. This funcation may not return all the items from the table if
     * there are more than the total number. The total number of items is also
     * returned from the system.
     * @param Request $request containing filter criteria
     * @return JsonResponse containing up to 10 locations requested from the
     *                      system
     */
    public function getAction(Request $request)
    {
        $limit = 10;
        $offset = 0;
        if ($request->query->get('limit') && $request->query->get('offset')) {
            $limit = $request->query->get('limit');
            $offset = $request->query->get('offset');
        }
        $searchTerm = $request->query->get('search');
        $em = $this->getDoctrine()->getManager();
        $response = new JsonResponse();
        $response->setData(
            Location::search($em, $searchTerm, $limit, $offset)
        );
        return $response;
    }

    /**
     * Get Notes
     * Gets notes for a location (optionally filtered).
     * @param int     $locationId the id of the location the notes should link to
     * @param Request $request    filtering for the location notes
     * @return JsonResponse
     */
    public function getNotesAction($locationId, Request $request)
    {
        $limit = 10;
        $offset = 0;
        if ($request->query->get('limit') && $request->query->get('offset')) {
            $limit = $request->query->get('limit');
            $offset = $request->query->get('offset');
        }
        $searchTerm = $request->query->get('search');
        $em = $this->getDoctrine()->getManager();
        $response = new JsonResponse();
        $response->setData(
            LocationNote::search($em, $locationId, $searchTerm, $limit, $offset)
        );
        return $response;
    }

    /**
     * Edit Location
     * The screen through which locations can be edited.
     * @param int     $id      id of the location to edit
     * @param Request $request containing the updated location information
     * @return HTML edit location page 
     */
    public function editAction($id, Request $request)
    {
        // Loads the location to edit
        $location = $this->getDoctrine()
            ->getRepository('AppBundle:Location')
            ->find($id);
        if (!$location) {
            throw $this->createNotFoundException(
                'Location not found. It may not exist or have been deleted.'
            );
        }
        
        // Carry out page functions if needed
        $form = $this->_formUpdate($request, $location);
        $lNForm = $this->_formLNAdd($id, $request);
        $formDelete = $this->_formDelete($request, $location);

        // Load the page or redirect to the locations
        if ($this->_redirectToLocations === true) {
            return $this->redirectToRoute('locations_manage');
        } else {
            $html = $this->container->get('templating')->render(
                'locations/edit.html.twig',
                array(
                    'formLocation' => $form->createView(),
                    'formLocationNote' => $lNForm->createView(),
                    'displayMessage' => $this->_displayMessage,
                    'formDelete' => $formDelete->createView(),
                )
            );
            return new Response($html);
        }
    }
}
