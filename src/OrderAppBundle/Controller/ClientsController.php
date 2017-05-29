<?php

namespace OrderAppBundle\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use OrderAppBundle\Entity\Clients;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Client controller.
 *
 * @Route("clients")
 */
class ClientsController extends Controller
{
    /**
     * Lists all client entities.
     *
     * @Route("/", name="clients_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $clients = $em->getRepository('OrderAppBundle:Clients')->findAll();

        return $this->render('clients/index.html.twig', array(
            'clients' => $clients,
        ));
    }

    /**
     * Creates a new client entity.
     *
     * @Route("/new", name="clients_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $client = new Clients();
        $form = $this->createForm('OrderAppBundle\Form\ClientsType', $client);
        $form->handleRequest($request);

        if ($request->getMethod() === "POST") {
            $client->setName($request->get('name'));
            $client->setSurname($request->get('surname'));
            $client->setCompany($request->get('company'));
            $client->setNip($request->get('nip'));

            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($client);
                $em->flush();
            }catch (UniqueConstraintViolationException $exception){

                $this->addFlash(
                    'error',
                    'Nip już istnieje w bazie!'
                );

                return $this->render('clients/new.html.twig', array(
                    'client' => $client,
                    'form' => $form->createView(),
                ));
            }


            return $this->redirectToRoute('clients_show', array('id' => $client->getId()));
        }

        return $this->render('clients/new.html.twig', array(
            'client' => $client,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a client entity.
     *
     * @Route("/{id}", name="clients_show")
     * @Method("GET")
     */
    public function showAction(Clients $client)
    {
        $deleteForm = $this->createDeleteForm($client);

        return $this->render('clients/show.html.twig', array(
            'client' => $client,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing client entity.
     *
     * @Route("/{id}/edit", name="clients_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Clients $client)
    {
        $deleteForm = $this->createDeleteForm($client);
        $editForm = $this->createForm('OrderAppBundle\Form\ClientsType', $client);
        $editForm->handleRequest($request);

        if ($request->getMethod() === "POST") {

            $client->setName($request->get('name'));
            $client->setSurname($request->get('surname'));
            $client->setCompany($request->get('company'));
            $client->setNip($request->get('nip'));

            try {
                $this->getDoctrine()->getManager()->flush();
            } catch(UniqueConstraintViolationException $exception){

                $this->addFlash(
                    'error',
                    'Nip już istnieje w bazie!'
                );

                return $this->render('clients/edit.html.twig', array(
                    'client' => $client,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }
            return $this->redirectToRoute('clients_edit', array('id' => $client->getId()));
        }

        return $this->render('clients/edit.html.twig', array(
            'client' => $client,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a client entity.
     *
     * @Route("/{id}", name="clients_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Clients $client)
    {
        $form = $this->createDeleteForm($client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($client);
            $em->flush();
        }

        return $this->redirectToRoute('clients_index');
    }

    /**
     * Creates a form to delete a client entity.
     *
     * @param Clients $client The client entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Clients $client)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('clients_delete', array('id' => $client->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
