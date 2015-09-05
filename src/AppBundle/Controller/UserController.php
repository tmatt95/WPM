<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

}
