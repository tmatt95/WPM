<?php

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

class PartsController extends Controller
{
    /**
     * Used to store notice messages to be displayed at the top of the 
     * manage/edit windows after an ection has been carried out.
     * 
     * `class`:
     * Class to be displayed on the alert box. Defaults to 'alert-success'.
     * 
     * `showButton`:
     * Whether to show the button linking to the location the message relates to.
     * Defaults to false.
     */
    private $displayMessage = array(
        'class' => 'alert-success',
        'showButton' => false,
        'buttonText' => 'Edit Part',
        'partId' => null,
        'value' => '',
    );

    public function partsAction()
    {
        return $this->redirectToRoute('parts_find');
    }

    public function getDatePartNumbersAction()
    {
        $em = $this->getDoctrine()->getManager();
        $queryDatePartNumbers = $em->createQuery(
            'SELECT pc.added_date,
                SUM(pc.no_added) - sum(pc.no_taken) AS number_added_removed
            FROM AppBundle:PartChange pc
            GROUP BY pc.added_date'
        );
        $datePartNumbers = $queryDatePartNumbers->getResult();

        $output = array();
        $total = 0;
        foreach ($datePartNumbers as $dpn) {
            $total = $total + $dpn['number_added_removed'];
            $output[] = array(
                'date' => $dpn['added_date'],
                'total' => $total,
            );
        }

        $response = new JsonResponse();
        $response->setData(
            $output
        );

        return $response;
    }

    public function indexAction()
    {
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
            'parts/index.html.twig', array('partsAdded' => $partsAdded, 'partsUpdated' => $partsUpdated)
        );

        return new Response($html);
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Generates the form
        $part = new Part();
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

            // Adds created date to form
            $createdDate = new DateTime('Europe/London');
            $part->setAdded($createdDate);
            $part->setAddedBy($this->getUser());

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

    public function searchAction(Request $request)
    {
        $limit = 10;
        $offset = 0;
        if ($request->query->get('limit') && $request->query->get('offset')) {
            $limit = $request->query->get('limit');
            $offset = $request->query->get('offset');
        }

        // Sets location id if sent to limit the search to a specific location
        $locationid = null;
        if ($request->query->get('locationid')) {
            $locationid = $request->query->get('locationid');
        }
        $searchTerm = $request->query->get('search');
        $em = $this->getDoctrine()->getManager();
        $response = new JsonResponse();
        $response->setData(
            Part::search($em, $searchTerm, $limit, $offset, $locationid)
        );

        return $response;
    }

    public function findAction()
    {
        $html = $this->container->get('templating')->render(
            'parts/find.html.twig', array()
        );

        return new Response($html);
    }

    public function viewAction($partId, Request $request)
    {
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
                $message = 'Successfuly added '.$partChange->getNoAdded();
            } else {
                $message = 'Successfuly taken '.$partChange->getNoTaken();
            }
            $this->displayMessage['value'] = $message.'. There are now '.$partChange->getNoTotal().' in the system.';
            $FPartChange = $this->createForm(new FPartChange(), new PartChange());
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
