<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\PartType;
use AppBundle\Form\Parts\PartType as FPartType;

class PartTypesController extends Controller {
    
    /**
     * Used to store notice messages to be displayed at the top of the 
     * manage/edit windows after an ection has been carried out.
     */
    private $displayMessage= array(
        'class'=>'alert-success',
        'value'=>''
    );
    
    public function getStatsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $response = new JsonResponse();
        $response->setData(
            PartType::getStats($em)
        );
        return $response;
    }

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
            PartType::search($em, $searchTerm, $limit, $offset)
        );
        return $response;
    }

    public function manageAction(Request $request) {
        $partType = new PartType();
        $form = $this->createForm(new FPartType(), $partType);
        $form->handleRequest($request);
        
        // If form is posted and valid, then saves
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($partType);
            $em->flush();
            $this->displayMessage['value'] = 'Successfuly added part type';
            $form = $this->createForm(new FPartType(), $partType);
        }

        $html = $this->container->get('templating')->render(
            'parttypes/manage.html.twig',
            array(
                'form'=>$form->createView(),
                'displayMessage'=>$this->displayMessage
            )
        );
        return new Response($html);
    }
    
    public function editAction($id,Request $request) {
        $partType = $this->getDoctrine()
                ->getRepository('AppBundle:PartType')
                ->find($id);
        $form = $this->createForm(new FPartType(), $partType);
        $form->handleRequest($request);
        
        // If form is posted and valid, then saves
        if ($form->isValid()) {
            
            //var_dump($form->get('save')->isClicked());
            $em = $this->getDoctrine()->getManager();
            $em->persist($partType);
            $em->flush();
            $this->displayMessage['value'] = 'Successfuly updated part type';
            $form = $this->createForm(new FPartType(), $partType);
        }

        $html = $this->container->get('templating')->render(
            'parttypes/edit.html.twig',
            array(
                'form'=>$form->createView(),
                'displayMessage'=>$this->displayMessage
            )
        );
        return new Response($html);
    }

}
