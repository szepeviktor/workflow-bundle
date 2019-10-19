<?php

namespace whatwedo\WorkflowBundle\Controller;

use Socius\Controller\AbstractController;
use Socius\Entity\Workflow\Transition;
use Socius\Entity\Workflow\TransitionEventDefinition;
use Socius\Form\Workflow\TransitionEventDefinitionType;
use Socius\Repository\Workflow\TransitionEventDefinitionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/workflow/transition/event/definition")
 */
class TransitionEventDefinitionController extends AbstractController
{

    /**
     * @Route("/new/{transition}", name="workflow_transition_event_definition_new", methods={"GET","POST"})
     */
    public function new(Request $request, Transition $transition): Response
    {
        $transitionEventDefinition = new TransitionEventDefinition($transition);
        $form = $this->createForm(TransitionEventDefinitionType::class, $transitionEventDefinition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($transitionEventDefinition);
            $entityManager->flush();

            return $this->redirectToRoute('workflow_transition_show', ['id' => $transitionEventDefinition->getTransition()->getId()]);
        }

        return $this->render('workflow/transition_event_definition/new.html.twig', [
            'eventDefinition' => $transitionEventDefinition,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="workflow_transition_event_definition_show", methods={"GET"})
     */
    public function show(TransitionEventDefinition $transitionEventDefinition): Response
    {
        return $this->render('workflow/transition_event_definition/show.html.twig', [
            'transition_event_definition' => $transitionEventDefinition,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="workflow_transition_event_definition_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TransitionEventDefinition $transitionEventDefinition): Response
    {
        $form = $this->createForm(TransitionEventDefinitionType::class, $transitionEventDefinition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('workflow_transition_show', ['id' => $transitionEventDefinition->getTransition()->getId()]);
        }

        return $this->render('workflow/transition_event_definition/new.html.twig', [
            'eventDefinition' => $transitionEventDefinition,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="workflow_transition_event_definition_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TransitionEventDefinition $transitionEventDefinition): Response
    {
        if ($this->isCsrfTokenValid('delete'.$transitionEventDefinition->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($transitionEventDefinition);
            $entityManager->flush();
        }

        return $this->redirectToRoute('workflow_transition_show', ['id' => $transitionEventDefinition->getTransition()->getId()]);
    }
}