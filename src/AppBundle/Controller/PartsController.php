<?php

// src/AppBundle/Controller/PartsController.php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Part;
use DateTime;

class PartsController extends Controller {

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
        // Gets types
        $repository = $this->getDoctrine()
                ->getRepository('AppBundle:PartType');
        $query = $repository->createQueryBuilder('pt')
                ->getQuery();
        $pt = $query->getResult();

        $list = [];
        foreach ($pt as $p) {
            $list[$p->getId()] = $p->getName();
        }

        // Gets locations
        $repository = $this->getDoctrine()
                ->getRepository('AppBundle:Location');
        $query = $repository->createQueryBuilder('l')
                ->getQuery();
        $ll = $query->getResult();

        $llist = [];
        foreach ($ll as $l) {
            $llist[$l->getId()] = $l->getName();
        }


        // Generates the form
        $part = new Part();
        $form = $this->createFormBuilder($part)
                ->add('name', 'text')
                ->add('description', 'textarea')
                ->add('type', 'choice', array(
                    'choices' => $list,
                    'required' => false,
                ))
                ->add('location', 'choice', array(
                    'choices' => $llist,
                    'required' => false,
                ))
                ->add('qty', 'integer')
                ->add('save', 'submit', array('label' => 'Add Part'))
                ->getForm();

        // Populates the form with submitted data if any present
        $form->handleRequest($request);

        // If form is posted and valid, then saves
        if ($form->isValid()) {

            // Adds missing information to form
            $createdDate = new DateTime('Europe/London');
            $part->setAdded($createdDate);
            $user = $this->getUser();
            $part->setAddedBy($user->getId());

            // Adds audit log entry
            // Saves the new part to the system
            $em = $this->getDoctrine()->getManager();
            $em->persist($part);
            $em->flush();
        }

        // Renders the add part screen
        $html = $this->container->get('templating')->render(
                'parts/add.html.twig', array('form' => $form->createView(), 'locations' => $llist, 'types' => $list)
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
