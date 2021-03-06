<?php

/**
 * Parts
 * The main entity in the application. All actions to do with managing parts
 * are done through this controller.
 *
 * PHP version 5.6
 *
 * @category WPM
 * @package  Part
 * @author   Matthew Turner <tmatt95@gmail.com>
 * @license  http://opensource.org/licenses/GPL-3.0 GPL3
 * @version  GIT: <1.0.0>
 * @link     https://github.com/tmatt95/WPM/
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Part;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Location;
use AppBundle\Entity\PartType;
use AppBundle\Form\Parts\PartChange as FPartChange;
use AppBundle\Entity\PartChange as PartChange;
use Exception;
use DateTimeZone;

/**
 * Parts
 * The main entity in the application. All actions to do with managing parts
 * are done through this controller.
 *
 * PHP version 5.6
 *
 * @category WPM
 * @package  Part
 * @author   Matthew Turner <tmatt95@gmail.com>
 * @license  http://opensource.org/licenses/GPL-3.0 GPL3
 * @version  Release: <1.0.0>
 * @link     https://github.com/tmatt95/WPM/
 */
class PartsController extends Controller {

    /**
     * Used to store notice messages to be displayed at the top of the
     * manage/edit windows after an action has been carried out.
     */
    private $displayMessage = array(
        'class' => 'alert-success',
        'showButton' => false,
        'buttonText' => 'Edit Part',
        'partId' => null,
        'value' => '',
    );

    /**
     * Used to enable people to load the parts find page when they go to /parts/
     * in the web browser.
     * @return Page redirect
     */
    public function partsAction() {
        return $this->redirectToRoute('parts_find');
    }

    /**
     * Get Date and Part Number Information
     * Gets information on the number of parts that are in the system on each
     * day changes were made.
     * @return JsonResponse
     */
    public function getDatePartNumbersAction() {
        // Generate the info
        $em = $this->getDoctrine()->getManager();
        $queryDatePartNumbers = $em->createQuery(
                'SELECT pc.added_date,
                SUM(pc.no_added) - sum(pc.no_taken) AS number_added_removed
            FROM AppBundle:PartChange pc
            GROUP BY pc.added_date'
        );
        $datePartNumbers = $queryDatePartNumbers->getResult();

        $output = array();
        if (count($datePartNumbers) > 0) {
            $total = 0;
            $dateChanges = array();

            // Creates an array of dates and changes which is easy to access
            // by date
            foreach ($datePartNumbers as $dpn) {
                $total = $total + $dpn['number_added_removed'];
                $dateChanges[date_format($dpn['added_date'], 'Y-m-d')] = $total;
            }

            // First date in the syste, and current date to go to
            $iDate = $datePartNumbers[0]['added_date'];
            $curDate = new DateTime('now', new DateTimeZone('UTC'));

            $runningTotal = 0;
            while ($iDate < $curDate) {

                // Updates the total with the new amount if there is an entry in
                // the results
                if (isset($dateChanges[date_format($iDate, 'Y-m-d')]) === true) {
                    $runningTotal = $dateChanges[date_format($iDate, 'Y-m-d')];
                }
                
                // Output to go to browser
                // Without cloning, the last date is returned to the browser
                $output[] = array(
                    'date' => clone($iDate),
                    'total' => $runningTotal,
                );

                // Adds one onto the incremental date
                date_add($iDate, date_interval_create_from_date_string('1 days'));
            }
        }

        // Output to browser
        $response = new JsonResponse();
        $response->setData(
                $output
        );
        return $response;
    }

    /**
     * Dashboard
     * The main dashboard of the application
     * @return HTML dashboard
     */
    public function indexAction() {
        // Information for the latest added parts widget
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
                'SELECT p.name,
            p.qty,
            p.added,
            u.id,
            u.name_first,
            u.name_last
            FROM AppBundle:Part p
            JOIN p.addeduser u'
        );
        $query->setMaxResults(10);
        $partsAdded = $query->getResult();

        $queryPartsUpdated = $em->createQuery(
                'SELECT pc.no_added,
            pc.no_taken,
            pc.added,
            pc.no_total,
            u.name_first,
            u.name_last,
            pi.name,
            pi.id AS part_id
            FROM AppBundle:PartChange pc
            JOIN pc.addeduser u
            JOIN pc.partInfo pi
            ORDER BY pc.added DESC'
        );
        $queryPartsUpdated->setMaxResults(10);
        $partsUpdated = $queryPartsUpdated->getResult();
        $html = $this->container->get('templating')->render(
                'parts/index.html.twig', array(
            'partsAdded' => $partsAdded,
            'partsUpdated' => $partsUpdated
                )
        );
        return new Response($html);
    }

    /**
     * Add Part
     * Add a new part to the system
     * @param Request $request may contain new part information
     * @return HTML add part page
     */
    public function addAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        // Generates the form
        $part = new Part();
        
        // Adds created date to form
        $createdDate = new DateTime('Europe/London');
        $part->setAdded($createdDate);
        $part->setAddedBy($this->getUser()->getId());
        $part->setAddeduser($this->getUser());
        
        $form = $this->createFormBuilder($part)
                ->add('name', 'text')
                ->add('description', 'textarea')
                ->add(
                        'type', 'choice', array(
                    'choices' => PartType::getList($em),
                    'required' => false,
                        )
                )
                ->add(
                        'location', 'choice', array(
                    'choices' => Location::getList($em),
                    'required' => false,
                        )
                )
                ->add('qty', 'integer')
                ->add('save', 'submit', array('label' => 'Add Part'))
                ->getForm();
        $blankForm = clone($form);

        // Populates the form with submitted data if any present
        $form->handleRequest($request);

        
        // If form is posted and valid, then saves
        if ($form->isValid()) {
            
                    $part->setParttype(
                $this->getDoctrine()
                    ->getRepository('AppBundle:PartType')
                    ->find($part->getType())
        );

        $part->setLocationinfo(
                $this->getDoctrine()
                    ->getRepository('AppBundle:Location')
                    ->find($part->getLocation())
        );

            // Saves the new part to the system
            $em = $this->getDoctrine()->getManager();
            $em->persist($part);
            $em->flush();
            $this->displayMessage['value'] = 'Successfuly added part';
            $this->displayMessage['showButton'] = true;
            $this->displayMessage['partId'] = $part->getId();
            $partChange = new PartChange();
            $partChange->setPartInfo($part);
            $partChange->setNoAdded($part->getQty());
            $partChange->setNoTaken(0);
            $partChange->setNoTotal($part->getQty());
            $partChange->setAddeduser($this->getUser());
            $partChange->setComment('Part Added');
            $partChange->setAddedDate($createdDate);
            $partChange->setAdded($createdDate);
            $partChange->setType(0);
            $partChange->setAddedlocation($part->getLocationinfo());
            $em->persist($partChange);
            $em->flush();
            $form = $blankForm;
        }

        // Renders the add part screen
        $html = $this->container->get('templating')->render(
                'parts/add.html.twig', array(
            'form' => $form->createView(),
            'displayMessage' => $this->displayMessage,
                )
        );
        return new Response($html);
    }

    /**
     * Search Parts
     * Search parts in the system.
     * @param Request $request optional filters
     * @return JsonResponse containing parts found in the system
     */
    public function searchAction(Request $request) {
        $limit = 10;
        $offset = 0;
        if ($request->query->get('limit') && $request->query->get('offset')) {
            $limit = $request->query->get('limit');
            $offset = $request->query->get('offset');
        }

        // Sets location id if sent to limit the search to a specific location
        $locationid = null;
        if ($request->query->get('locationid')) {
            if($request->query->get('locationid') !== ''){
                $locationid = $request->query->get('locationid');
            }
        }
        
        // Sets if we should look for parts which have a qty greater than 0 only
        $hasqty = null;
        if ($request->query->get('hasqty')) {
            if($request->query->get('hasqty') !== ''){
                $hasqty = $request->query->get('hasqty');
            }
        }
        
        // Sets if we should look for parts which have a qty greater than 0 only
        $partType = null;
        if ($request->query->get('parttype')) {
            if($request->query->get('parttype') !== ''){
                $partType = $request->query->get('parttype');
            }
        }
        
        $searchTerm = $request->query->get('name');
        $em = $this->getDoctrine()->getManager();
        $response = new JsonResponse();
        $response->setData(
                Part::search($em, $searchTerm, $limit, $offset, $locationid, $partType, $hasqty)
        );
        return $response;
    }

    /**
     * Find Parts
     * It is through this page that you can find parts in the system.
     * @return HTML Find parts screen
     */
    public function findAction() {
        $em = $this->getDoctrine()->getManager();
        $locations = Location::getList($em);
        $partTypes = PartType::getList($em);
        $html = $this->container->get('templating')->render(
                'parts/find.html.twig', array('locations'=>$locations , 'parttypes'=>$partTypes)
        );
        return new Response($html);
    }

    /**
     * View Part
     * Used to manage an individual part information.
     * @param int     $partId  id of part
     * @param Request $request may contain forms for updating the part
     * @return Response HTML
     * @throws Exception if the part could not be found
     * @throws Exception if updating the part would result in it having a
     * negative qty
     */
    public function viewAction($partId, Request $request) {
        $em = $this->getDoctrine()->getManager();
        // Loads part information
        $part = $this->getDoctrine()
                ->getRepository('AppBundle:Part')
                ->find($partId);

        if (!$part) {
            throw $this->createNotFoundException(
                    'Part not found. It may not exist or have been deleted.'
            );
        }

        $partChange = new PartChange();
        $FPartChange = $this->createForm(new FPartChange(), $partChange);
        $FPartChange->handleRequest($request);
        if ($FPartChange->isValid()) {
            // Calculates values which need to be added from the server side
            $noTotal = $part->getQty() - $partChange->getNoTaken() + $partChange->getNoAdded();
            if ($noTotal < 0) {
                throw new Exception('You do not have enough parts!', 400);
            }
            $em = $this->getDoctrine()->getManager();
            $createdDate = new DateTime('Europe/London');
            $partChange->setAdded($createdDate);
            $partChange->setAddedDate($createdDate);
            $partChange->setAddeduser($this->getUser());
            $partChange->setPartInfo($part);
            $partChange->setAddedlocation($part->getLocationInfo());
            $partChange->setNoTotal($noTotal);

            // Updates the total number on the part
            $part->setQty($noTotal);
            $em->persist($part);
            $em->persist($partChange);
            $em->flush();

            $message = '';
            if ($partChange->getType() === 0) {
                $message = 'Successfuly added ' . $partChange->getNoAdded();
            } else {
                $message = 'Successfuly taken ' . $partChange->getNoTaken();
            }
            $this->displayMessage['value'] = $message
                    . '. There are now '
                    . $partChange->getNoTotal()
                    . ' in the system.';
            $FPartChange = $this->createForm(
                    new FPartChange(), new PartChange()
            );
        }

        $form = $this->createFormBuilder($part)
                ->add('name', 'text')
                ->add('description', 'textarea')
                ->add(
                        'type', 'choice', array(
                    'choices' => PartType::getList($em),
                    'required' => false,
                        )
                )
                ->add(
                        'location', 'choice', array(
                    'choices' => Location::getList($em),
                    'required' => false,
                        )
                )
                ->add('save', 'submit', array('label' => 'Update Part'))
                ->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($part);
            $em->flush();
            $this->displayMessage['value'] = 'Successfuly updated part';
        }

        $queryPartHistory = $em->createQuery(
                'SELECT pc.no_added,
            pc.no_taken,
            pc.added,
            pc.no_total,
            pc.comment,
            u.name_first,
            u.name_last,
            pi.name,
            pi.id AS part_id
            FROM AppBundle:PartChange pc
            JOIN pc.addeduser u
            JOIN pc.partInfo pi
            WHERE pc.part_id = :partid
            ORDER BY pc.added DESC'
        );
        $queryPartHistory->setParameter(
                ':partid', $partId
        );
        $partHistory = $queryPartHistory->getResult();

        $html = $this->container->get('templating')->render(
                'parts/view.html.twig', array(
            'part' => $part,
            'FPartChange' => $FPartChange->createView(),
            'form' => $form->createView(),
            'displayMessage' => $this->displayMessage,
            'partHistory' => $partHistory,
                )
        );
        return new Response($html);
    }

}
