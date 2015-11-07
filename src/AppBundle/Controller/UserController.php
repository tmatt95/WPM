<?php

/**
 * User Controller
 * Users are the people who use the system. Most actions that involve parts will
 * have a user attached to it when recorded in the system. This file is for the
 * management of users.
 *
 * PHP version 5.6
 *
 * @category WPM
 * @package  User
 * @author   Matthew Turner <tmatt95@gmail.com>
 * @license  http://opensource.org/licenses/GPL-3.0 GPL3
 * @version  GIT: <1.0.0>
 * @link     https://github.com/tmatt95/WPM/
 */

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
 * management of users.
 *
 * PHP version 5.6
 *
 * @category WPM
 * @package  User
 * @author   Matthew Turner <tmatt95@gmail.com>
 * @license  http://opensource.org/licenses/GPL-3.0 GPL3
 * @version  Release: <1.0.0>
 * @link     https://github.com/tmatt95/WPM/
 */
class UserController extends Controller
{
    /**
     * Used to store notice messages to be displayed at the top of the
     * manage/edit windows after an action has been carried out.
     * @var array
     */
    private $_displayMessage = array(
        'class' => 'alert-success',
        'showButton' => false,
        'buttonText' => 'Edit User',
        'userId' => null,
        'value' => '',
    );

    /**
     * Whether to redirect to the users page or render the view.
     * @var bool defaults to false
     */
    private $_redirectToUsers = false;

    /**
     * Form Add
     * Will try and add a new user to the system if there is one to add.
     * @param Request $request may containing the user form if one present
     * @return FLocation either a blank form or one with errors
     */
    private function _formAdd(Request $request)
    {
        // Adds created date
        $createdDate = new DateTime('Europe/London');
        $user = new User();
        $user->setAdded($createdDate);

        // The users who added the new user to the system
        $luser = $this->getUser();
        $user->setAddedBy($luser->getId());

        // Populates form if form present to populate
        $form = $this->createForm(new FUser(), $user);
        $form->handleRequest($request);

        // If form is posted and valid, then saves
        if ($form->isValid()) {
            $encoder = $this->container->get('security.password_encoder');
            $user->setPassword(
                $encoder->encodePassword($user, $user->getPassword())
            );
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->_displayMessage['value'] = 'Successfuly added user';
            $this->_displayMessage['showButton'] = true;
            $this->_displayMessage['userId'] = $user->getId();
            $form = $this->createForm(new FUser(), new User());
        }
        return $form;
    }

    /**
     * Form Edit
     * Used to Update every field on the user form apart from the password.
     * @param Request  $request containing the POST data if sent
     * @param Location $user    record to be added
     * @return FLocation
     */
    private function _formEdit(Request $request, User $user)
    {
        // Populates form if one sent to application
        $form = $this->createForm(new FUserUpdate(), $user);
        $form->handleRequest($request);

        // If form is posted and valid, then saves
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->_displayMessage['value'] = 'Successfuly updated user';
        }
        return $form;
    }

    /**
     * Form Delete
     * If a delete user form is posted to the application, then this will
     * process it.
     * @param Request $request containing the POST data if sent
     * @param User    $user    record to be deleted if it needs to be
     * @return FUserDelete
     */
    private function _formDelete(Request $request, User $user)
    {
        // Populates form if one sent to the application
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
            $this->_redirectToUsers = true;
        }
        return $form;
    }

    /**
     * Form Edit Password
     * Updates the password of the linked user with the new one supplied
     * through the request.
     * @param Request $request containing the update password form
     * @param User    $user    to update the password on
     * @return FUserPassword
     */
    private function _formEditPassword(Request $request, User $user)
    {
        // Populates form if one sent to the application
        $form = $this->createForm(new FUserPassword(), $user);
        $form->handleRequest($request);

        // If form is posted and valid, then saves
        if ($form->isValid()) {
            $encoder = $this->container->get('security.password_encoder');
            $user->setPassword(
                $encoder->encodePassword($user, $user->getPassword())
            );
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $message = 'Successfuly updated user password';
            $this->_displayMessage['value'] = $message;
        }
        return $form;
    }

    /**
     * Add New User
     * Will display the add new user form. If a new user has been posted to this
     * action then it will try and add it, returning a blank new user form.
     * @param Request $request (optional) containing the new user form
     * @return Response HTML add user page
     */
    public function addAction(Request $request)
    {
        $form = $this->_formAdd($request);
        $html = $this->container->get('templating')->render(
            'users/add.html.twig',
            array(
                'form' => $form->createView(),
                'displayMessage' => $this->_displayMessage,
            )
        );
        return new Response($html);
    }

    /**
     * Edit User
     * @param type    $userId  ID of the user to edit
     * @param Request $request may contain user update/delete/password forms
     * @return Response HTML user edit page
     * @throws Excpetion if User cannot be found
     */
    public function editAction($userId, Request $request)
    {
        // Loads the user record
        $user = $this->getDoctrine()->getRepository('AppBundle:User')
            ->find($userId);
        if (!$user || $user->getDeleted() === 1) {
            throw $this->createNotFoundException(
                'User not found. It may not exist or have been deleted.'
            );
        }
        
        // Process any of the forms if there are any to process
        $formPassword = $this->_formEditPassword($request, $user);
        $formUser = $this->_formEdit($request, $user);
        $formDelete = $this->_formDelete($request, $user);

        // Redirect to users page or load up the user
        if ($this->_redirectToUsers === true) {
            return $this->redirectToRoute('users_manage');
        }
        $html = $this->container->get('templating')->render(
            'users/edit.html.twig',
            array(
                'form' => $formUser->createView(),
                'formPassword' => $formPassword->createView(),
                'displayMessage' => $this->_displayMessage,
                'formDelete' => $formDelete->createView(),
            )
        );
        return new Response($html);
    }

    /**
     * Manage User
     * The page containing the list of all the users in the system.
     * @return Response HTML the manage user page
     */
    public function manageAction()
    {
        $html = $this->container->get('templating')
            ->render('users/manage.html.twig');
        return new Response($html);
    }

    /**
     * Get User List
     * Finds a list of users in the database which are not marked as
     * deleted. The users found are returned based on the supplied limit and
     * offset. The total number of users is also returned from the system.
     * @param Request $request with POST information
     * @return JsonResponse containing up to 10 users requested from the
     *                      system and a total number of users
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
            User::search($em, $searchTerm, $limit, $offset)
        );
        return $response;
    }
}
