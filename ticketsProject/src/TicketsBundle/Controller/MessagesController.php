<?php

namespace TicketsBundle\Controller;

use TicketsBundle\Entity\Messages;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Message controller.
 *
 * @Route("messages")
 */
class MessagesController extends Controller
{
    /**
     * Creates a new message entity.
     *
     * @Route("/new/{ticket_id}", name="messages_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, $ticket_id)
    {
        $message = new Messages();
        $ticket = $this->getDoctrine()->getRepository('TicketsBundle:Tickets')->findOneById($ticket_id);
        $message->setTicket($ticket);
        $message->setUser($this->getUser());
        $form = $this->createForm('TicketsBundle\Form\MessagesType', $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush($message);
            return $this->redirectToRoute('tickets_show', array('id' => $message->getTicket()->getId()));
        }

        return $this->render('messages/new.html.twig', array(
            'message' => $message,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing message entity.
     *
     * @Route("/{id}/edit", name="messages_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Messages $message)
    {
        if (!$this->getUser()->hasRole('ROLE_ADMIN')){
            return $this->redirectToRoute('tickets_index');
        }
        $deleteForm = $this->createDeleteForm($message);
        $editForm = $this->createForm('TicketsBundle\Form\MessagesType', $message);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('tickets_show', array('id' => $message->getTicket()->getId()));
        }

        return $this->render('messages/edit.html.twig', array(
            'message' => $message,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a message entity.
     *
     * @Route("/{id}", name="messages_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Messages $message)
    {
        if (!$this->getUser()->hasRole('ROLE_ADMIN')){
            return $this->redirectToRoute('tickets_index');
        }
        $form = $this->createDeleteForm($message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($message);
            $em->flush($message);
        }

        return $this->redirectToRoute('tickets_show', array('id' => $message->getTicket()->getId()));
    }

    /**
     * Creates a form to delete a message entity.
     *
     * @param Messages $message The message entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Messages $message)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('messages_delete', array('id' => $message->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
