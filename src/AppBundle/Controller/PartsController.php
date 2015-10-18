<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Part;
use DateTime;
use AppBundle\Entity\Location;
use AppBundle\Entity\PartType;

class PartsController extends Controller {

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
     * 
     *  
     */
    private $displayMessage = array(
        'class' => 'alert-success',
        'showButton' => false,
        'buttonText' => 'Edit Part',
        'partId' => null,
        'value' => ''
    );

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
            JOIN p.addeduser u');
        $query->setMaxResults(10);
        $partsAdded = $query->getResult();
        $html = $this->container->get('templating')->render(
                'parts/index.html.twig', array('partsAdded' => $partsAdded, 'partsUsed' => array())
        );
        return new Response($html);
    }

    public function addAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        // Generates the form
        $part = new Part();
        $form = $this->createFormBuilder($part)
                ->add('name', 'text')
                ->add('description', 'textarea')
                ->add('type', 'choice', array(
                    'choices' => PartType::getList($em),
                    'required' => false,
                ))
                ->add('location', 'choice', array(
                    'choices' => Location::getList($em),
                    'required' => false,
                ))
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

            // Loads the part type to link to the part
            $partType = $this->getDoctrine()
                    ->getRepository('AppBundle:PartType')
                    ->find($part->getType());
            $part->setType($partType);

            // Saves the new part to the system
            $em = $this->getDoctrine()->getManager();
            $em->persist($part);
            $em->flush();
            $this->displayMessage['value'] = 'Successfuly added part';
            $this->displayMessage['showButton'] = true;
            $this->displayMessage['partId'] = $part->getId();
            $form = $blankForm;
        }

        // Renders the add part screen
        $html = $this->container->get('templating')->render(
                'parts/add.html.twig', array(
            'form' => $form->createView(),
            'displayMessage' => $this->displayMessage
                )
        );
        return new Response($html);
    }

    public function findAction() {
        $html = $this->container->get('templating')->render(
                'parts/find.html.twig', array()
        );
        return new Response($html);
    }

    public function manageAction() {
        $html = $this->container->get('templating')->render(
                'parts/manage.html.twig', array()
        );
        return new Response($html);
    }

    public function viewAction($partId) {
        // Loads part information
        $part = $this->getDoctrine()
                ->getRepository('AppBundle:Part')
                ->find($partId);

        $html = $this->container->get('templating')->render(
                'parts/view.html.twig', array('part' => $part)
        );
        return new Response($html);
    }

}
