<?php

  // src/AppBundle/Controller/LocationsController.php
  namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use AppBundle\Entity\Location;
use AppBundle\Form\Locations\Location as FLocation;

class LocationsController extends Controller
{

  public function addAction(Request $request)
  {

    // Generates the form
    $location = new Location();
    $form = $this->createForm(new FLocation(),$location);
    $form->handleRequest($request);

    // If form is posted and valid, then saves
    if ($form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($location);
      $em->flush();
    }

    $html = $this->container->get('templating')->render(
      'locations/add.html.twig',
       array('form'=>$form->createView())
    );
    return new Response($html);
  }

  public function manageAction()
  {
    $html = $this->container->get('templating')->render(
      'locations/manage.html.twig',
      array()
    );
    return new Response($html);
  }

  public function getAction()
  {
    // Gets locations
    $repository = $this->getDoctrine()
    ->getRepository('AppBundle:Location');
    $query = $repository->createQueryBuilder('l')
    ->getQuery();
    $locations = $query->getResult();

    //Outputs to browser
    $output = [];
    foreach($locations as $l){
      $output[] = [
        'id'=>$l->getId(),
        'name'=>$l->getName(),
        'description' => $l->getDescription()
      ];
    }
    $response = new JsonResponse();
    $response->setData(
      $output
    );
    return $response;
  }

  public function partsinAction()
  {
    $html = $this->container->get('templating')->render(
      'locations/partsin.html.twig',
      array()
    );
    return new Response($html);
  }
}
