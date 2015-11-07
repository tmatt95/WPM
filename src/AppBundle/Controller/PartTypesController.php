<?php

/**
 * Part Types
 * Part types are generally describe what the part is. This controller allows
 * them to be managed and also is where all information which primarily
 * relates to them is stored.
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
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\PartType;
use AppBundle\Entity\Part;
use AppBundle\Form\Parts\PartType as FPartType;
use AppBundle\Form\Parts\PartTypeUpdate as FPartTypeUpdate;
use AppBundle\Form\Parts\PartTypeDelete as FPartTypeDelete;

/**
 * Part Types
 * Part types are generally describe what the part is. This controller allows
 * them to be managed and also is where all information which primarily
 * relates to them is stored.
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
class PartTypesController extends Controller
{
    private $redirectToPartTypes = false;

    /**
     * Used to store notice messages to be displayed at the top of the
     * manage/edit windows after an ection has been carried out.
     */
    private $displayMessage = array(
        'class' => 'alert-success',
        'value' => '',
    );

    /**
     * Get Stats
     * Finds out how many parts are currently linked to each part type in the
     * system.
     * @return JsonResponse of part type stats
     */
    public function getStatsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $response = new JsonResponse();
        $response->setData(
            PartType::getStats($em)
        );
        return $response;
    }

    /**
     * Get Part Types
     * Searches the system for all part types which match the filters being
     * sent into the application.
     * @param Request $request containing the information used to filter the
     * search
     * @return JsonResponse of search responses
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
            PartType::search($em, $searchTerm, $limit, $offset)
        );
        return $response;
    }

    /**
     * Manage Part Types
     * From this page a user can add and select part types to edit.
     * @param Request $request may contain a new user form
     * @return HTML [art type management page
     */
    public function manageAction(Request $request)
    {
        // If form is posted and valid, then saves
        $partType = new PartType();
        $form = $this->createForm(new FPartType(), $partType);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($partType);
            $em->flush();
            $this->displayMessage['value'] = 'Successfuly added part type';
            $form = $this->createForm(new FPartType(), $partType);
        }
        
        // If there are errors in the form then we want it open when the page 
        // loads
        $noErrors = count($form->getErrors(true, true));
        $addFormClass='hidden';
        $addButtonClass = '';
        if($noErrors > 0 ){
            $addFormClass = '';
            $addButtonClass = 'hidden';
        }

        $html = $this->container->get('templating')->render(
            'parttypes/manage.html.twig',
            array(
                'form' => $form->createView(),
                'displayMessage' => $this->displayMessage,
                'addFormClass' => $addFormClass,
                'addButtonClass' => $addButtonClass
            )
        );
        return new Response($html);
    }

    /**
     * Edit Part Type
     * This screen allows users to edit part type information.
     * @param Integer $id      of part type
     * @param Request $request may contain edit or delete form
     * @return HTML
     * @throws Exception if part type cannot be found
     */
    public function editAction($id, Request $request)
    {
        $partType = $this->getDoctrine()
            ->getRepository('AppBundle:PartType')
            ->find($id);

        if (!$partType) {
            throw $this->createNotFoundException(
                'Part type not found. It may not exist or have been deleted.'
            );
        }

        $form = $this->createForm(new FPartTypeUpdate(), $partType);
        $formDelete = $this->createForm(new FPartTypeDelete(), $partType);
        $form->handleRequest($request);

        // If form is posted and valid, then saves
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($partType);
            $em->flush();
            $this->displayMessage['value'] = 'Successfuly updated part type';
        }

        $formDelete->handleRequest($request);
        if ($formDelete->isValid()) {
            $em = $this->getDoctrine()->getManager();
            Part::moveAllOutPartType($em, $partType->getId());
            $em->remove($partType);
            $em->flush();
            $this->redirectToPartTypes = true;
        }

        if ($this->redirectToPartTypes === true) {
            return $this->redirectToRoute('part_types_manage');
        } else {
            $html = $this->container->get('templating')->render(
                'parttypes/edit.html.twig',
                array(
                    'form' => $form->createView(),
                    'formDelete' => $formDelete->createView(),
                    'displayMessage' => $this->displayMessage,
                )
            );
            return new Response($html);
        }
    }
}
