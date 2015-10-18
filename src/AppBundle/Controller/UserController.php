<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\User;
use AppBundle\Form\User\User as FUser;
use AppBundle\Form\User\UserUpdate as FUserUpdate;
use AppBundle\Form\User\UserPassword as FUserPassword;
use AppBundle\Form\User\UserDelete as FUserDelete;
use DateTime;

/**
 * User Controller
 * Users are the people who use the system. Most actions that involve parts will
 * have a user attached to it when recorded in the system. This file is for the
 * management of users. All these actions should only be visible to 
 * administrators. 
 */
class UserController extends Controller {

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
     */
    private $displayMessage = array(
        'class' => 'alert-success',
        'showButton' => false,
        'buttonText' => 'Edit User',
        'userId' => null,
        'value' => ''
    );
    
    /**
     * User Add
     * Will try and add a new user to the system if there is one to add.
     * @param Request $request containing the POST data if sent
     * @param Location $user record to be added
     * @return FLocation Either a blank new for on success orone with errors
     */
    private function formAdd(Request $request, User $user) {
        // Adds created date of now
        $createdDate = new DateTime('Europe/London');
        $user->setAdded($createdDate);
        
        // The users who added the new user to the system
        $luser = $this->getUser();
        $user->setAddedBy($luser->getId());
        
        $form = $this->createForm(new FUser(), $user);
        $form->handleRequest($request);

        // If form is posted and valid, then saves
        if ($form->isValid()) {
            $encoder = $this->container->get('security.password_encoder');
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));

            // Saves the new record
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->displayMessage['value'] = 'Successfuly added user';
            $this->displayMessage['showButton'] = true;
            $this->displayMessage['userId'] = $user->getId();
            $form = $this->createForm(new FUser(), new User());
        } 
        return $form;
    }
    
    /**
     * Location Add
     * Will try and add a new location to the system if there is one to add.
     * @param Request $request containing the POST data if sent
     * @param Location $user record to be added
     * @return FLocation Either a blank new for on success orone with errors
     */
    private function formEdit(Request $request, User $user) {
        $form = $this->createForm(new FUserUpdate(), $user);
        $form->handleRequest($request);

        // If form is posted and valid, then saves
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->displayMessage['value'] = 'Successfuly updated user';
        } 
        return $form;
    }
    
    private function formDelete(Request $request, User $user) {
        $form = $this->createForm(new FUserDelete(), $user);
        $form->handleRequest($request);

        // If form is posted and valid, then delete user
        if ($form->isValid()) {
            $user->setDeleted(1);
            $luser = $this->getUser();
            $user->setDeletedBy($luser->getId());
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        } 
        return $form;
    }
    
    private function formEditPassword(Request $request, User $user) {
        $form = $this->createForm(new FUserPassword(), $user);
        $form->handleRequest($request);

        // If form is posted and valid, then saves
        if ($form->isValid()) {
            $encoder = $this->container->get('security.password_encoder');
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->displayMessage['value'] = 'Successfuly updated user';
        } 
        return $form;
    }

    /**
     * Add new user
     * Will display the add new user form. If a new user has been posted to this
     * action then it will try and add it, returning a blank new user form or a
     * filled in form if any errors are found.
     * @param Request $request (optional) containing the POSTED new user form
     * @return Response HTML for new user form
     */
    public function addAction(Request $request) {
        $form = $this->formAdd($request, new User());
        $html = $this->container->get('templating')->render(
            'users/add.html.twig', array('form' => $form->createView(), 'displayMessage' => $this->displayMessage)
        );
        return new Response($html);
    }

    public function editAction($userId, Request $request) {
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->find($userId);  
        $formPassword = $this->formEditPassword($request, $user);
        $formUser = $this->formEdit($request, $user);
        $formDelete = $this->formDelete($request, $user);

        $html = $this->container->get('templating')->render(
            'users/edit.html.twig',
            array(
                'form' => $formUser->createView(),
                'formPassword'=>$formPassword->createView(),
                'displayMessage'=>$this->displayMessage,
                'formDelete'=>$formDelete->createView()
            )
        );
        return new Response($html);
    }

    /**
     * Manage user page
     * This page is where the user administration takes place. 
     * @return Response user admin HTML
     */
    public function manageAction() {
        $html = $this->container->get('templating')->render(
                'users/manage.html.twig', array()
        );
        return new Response($html);
    }

    /**
     * Get user list
     * Finds a list of users in the database which are not marked as 
     * deleted. The users found are returned based on the supplied limit and
     * offset. The total number of users is also returned from the system.
     * @return JsonResponse containing up to 10 users requested from the
     * system and a total number of users
     */
    public function getAction(Request $request) {
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
                User::search($em, $searchTerm, $limit, $offset)
        );
        return $response;
    }

}
