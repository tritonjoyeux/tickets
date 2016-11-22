<?php

namespace TicketsBundle\Controller;

use TicketsBundle\Entity\Messages;
use TicketsBundle\Entity\Tickets;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Ticket controller.
 *
 * @Route("tickets")
 */
class TicketsController extends Controller
{
    /**
     * Lists all ticket entities.
     *
     * @Route("/", name="tickets_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tickets = $em->getRepository('TicketsBundle:Tickets')->findAll();

        return $this->render('tickets/index.html.twig', array(
            'tickets' => $tickets,
        ));
    }

    /**
     * Creates a new ticket entity.
     *
     * @Route("/new", name="tickets_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $ticket = new Tickets();
        $ticket->addUser($this->getUser());
        $form = $this->createForm('TicketsBundle\Form\TicketsType', $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ticket);
            $em->flush($ticket);

            return $this->redirectToRoute('tickets_show', array('id' => $ticket->getId()));
        }

        return $this->render('tickets/new.html.twig', array(
            'ticket' => $ticket,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ticket entity.
     *
     * @Route("/{id}", name="tickets_show")
     * @Method("GET")
     */
    public function showAction(Tickets $ticket)
    {
        $deleteForm = $this->createDeleteForm($ticket);
        $messages = $this->getDoctrine()->getRepository('TicketsBundle:Messages')->findByTicket($ticket->getId());
        $deleteMessage = [];
        foreach ($messages as $message){
           $deleteMessage[$message->getId()] = $this->createDeleteMessage($message)->createView();
        }
        return $this->render('tickets/show.html.twig', array(
            'ticket' => $ticket,
            'delete_form' => $deleteForm->createView(),
            'delete_message' => $deleteMessage,
            'messages' => $messages,
        ));
    }

    /**
     * Displays a form to edit an existing ticket entity.
     *
     * @Route("/{id}/edit", name="tickets_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Tickets $ticket)
    {
        if (!$this->getUser()->hasRole('ROLE_ADMIN')){
            return $this->redirectToRoute('tickets_index');
        }
        $deleteForm = $this->createDeleteForm($ticket);
        $editForm = $this->createForm('TicketsBundle\Form\TicketsType', $ticket);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tickets_edit', array('id' => $ticket->getId()));
        }

        return $this->render('tickets/edit.html.twig', array(
            'ticket' => $ticket,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ticket entity.
     *
     * @Route("/{id}", name="tickets_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Tickets $ticket)
    {
        if (!$this->getUser()->hasRole('ROLE_ADMIN')){
            return $this->redirectToRoute('tickets_index');
        }
        $form = $this->createDeleteForm($ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ticket);
            $em->flush($ticket);
        }

        return $this->redirectToRoute('tickets_index');
    }

    /**
     * Creates a form to delete a ticket entity.
     *
     * @param Tickets $ticket The ticket entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Tickets $ticket)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tickets_delete', array('id' => $ticket->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    private function createDeleteMessage(Messages $message)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('messages_delete', array('id' => $message->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
