<?php

namespace whatwedo\WorkflowBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use whatwedo\WorkflowBundle\Entity\Transition;
use whatwedo\WorkflowBundle\Entity\Place;
use whatwedo\WorkflowBundle\Entity\EventDefinition;
use whatwedo\WorkflowBundle\Form\EventDefinitionType;
use whatwedo\WorkflowBundle\Form\PlaceEventDefinitionType;
use whatwedo\WorkflowBundle\Repository\EventDefinitionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/wwd/workflow/event/definition")
 */
class EventDefinitionController extends AbstractController
{

    /**
     * @Route("/place/new/{place}", name="wwd_workflow_place_event_definition_new", methods={"GET","POST"})
     * @Route("/new/{transition}", name="wwd_workflow_event_definition_new", methods={"GET","POST"})
     */
    public function new(Request $request, Transition $transition = null, Place $place = null): Response
    {
        $eventDefinition = new EventDefinition();
        if ($transition) {
            $eventDefinition->setTransition($transition);
            $type = EventDefinitionType::class;
        }
        if ($place) {
            $eventDefinition->setPlace($place);
            $type = PlaceEventDefinitionType::class;
        }

        $form = $this->createForm($type, $eventDefinition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($eventDefinition);
            $entityManager->flush();

            return $this->redirectToRoute('wwd_workflow_event_definition_edit', ['id' => $eventDefinition->getId()]);
        }

        return $this->render('@whatwedoWorkflow/event_definition/new.html.twig', [
            'eventDefinition' => $eventDefinition,
            'form' => $form->createView(),
        ]);
    }

     /**
     * @Route("/{id}/edit", name="wwd_workflow_event_definition_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EventDefinition $eventDefinition): Response
    {
        if ($eventDefinition->getTransition()) {
            $type = EventDefinitionType::class;
        }
        if ($eventDefinition->getPlace()) {
            $type = PlaceEventDefinitionType::class;
        }
        $form = $this->createForm($type, $eventDefinition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('wwd_workflow_event_definition_edit', ['id' => $eventDefinition->getId()]);
        }

        return $this->render('@whatwedoWorkflow/event_definition/new.html.twig', [
            'eventDefinition' => $eventDefinition,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="wwd_workflow_event_definition_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EventDefinition $eventDefinition): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eventDefinition->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eventDefinition);
            $entityManager->flush();
        }

        return $this->redirectToRoute('wwd_workflow_workflow_show', ['id' => $eventDefinition->getTransition()->getWorkflow()->getId()]);
    }
}
