<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\User;
use AppBundle\Form\User\User as FUser;
use DateTime;

class UserController extends Controller {

    public function addAction(Request $request) {
        // Generates the form
        $user = new User();
        $form = $this->createForm(new FUser(), $user);
        $form->handleRequest($request);

        // If form is posted and valid, then saves
        if ($form->isValid()) {

            $encoder = $this->container->get('security.password_encoder');
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));

            // Adds created date of now
            $createdDate = new DateTime('Europe/London');
            $user->setAdded($createdDate);

            $luser = $this->getUser();
            $user->setAddedBy($luser->getId());

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }

        $html = $this->container->get('templating')->render(
                'users/add.html.twig', array('form' => $form->createView())
        );
        return new Response($html);
    }

    public function manageAction(Request $request) {
        $html = $this->container->get('templating')->render(
                'users/manage.html.twig', array()
        );
        return new Response($html);
    }
    
    /**
     * Get user list
     * Finds a list of users in the database which are not marked as 
     * deleted. The items found are returned based on the supplied limit and
     * offset. This funcation may not return all the items from the table if
     * there are more than the total number. The total number of items is also
     * returned from the system.
     * @return JsonResponse containing up to 10 users requested from the
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
            User::search($em,$searchTerm,$limit,$offset)
        );
        return $response;
    }

}
