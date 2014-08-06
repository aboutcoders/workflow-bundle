<?php

namespace Abc\Bundle\WorkflowBundle\Controller;

use Abc\Bundle\WorkflowBundle\Doctrine\WorkflowManager;
use Abc\Bundle\WorkflowBundle\Entity\Workflow;
use Abc\Bundle\WorkflowBundle\Form\WorkflowType;
use Abc\Bundle\WorkflowBundle\Model\WorkflowInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Workflow controller.
 *
 * @Route("/workflow")
 */
class WorkflowController extends BaseController
{
    /**
     * Lists all Workflow entities.
     *
     * @Route("/", name="workflow")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        return array(
            'entities' => $this->getWorkflowManager()->findAll()
        );
    }

    /**
     * Creates a new Workflow entity.
     *
     * @Route("/", name="workflow_create")
     * @Method("POST")
     * @Template("AbcWorkflowBundle:Workflow:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $workflowManager = $this->getWorkflowManager();
        $entity          = $workflowManager->create();
        $form            = $this->createCreateForm($entity);

        $form->handleRequest($request);

        if($form->isValid())
        {
            $workflowManager->update($entity);

            return $this->redirect($this->generateUrl('workflow_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Workflow entity.
     *
     * @Route("/new", name="workflow_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $workflowManager = $this->getWorkflowManager();
        $entity          = $workflowManager->create();
        $form            = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Workflow entity.
     *
     * @Route("/{id}", name="workflow_show")
     * @Method("GET")
     * @Template()
     * @throws NotFoundHttpException
     */
    public function showAction($id)
    {
        $entity     = $this->findWorkflow($id);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Workflow entity.
     *
     * @Route("/{id}/edit", name="workflow_edit")
     * @Method("GET")
     * @Template()
     * @throws NotFoundHttpException
     */
    public function editAction($id)
    {
        $entity     = $this->findWorkflow($id);
        $editForm   = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Workflow entity.
     *
     * @Route("/{id}", name="workflow_update")
     * @Method("PUT")
     * @Template("AbcWorkflowBundle:Workflow:edit.html.twig")
     * @throws NotFoundHttpException
     */
    public function updateAction(Request $request, $id)
    {
        $entity     = $this->findWorkflow($id);
        $deleteForm = $this->createDeleteForm($id);
        $editForm   = $this->createEditForm($entity);

        $editForm->handleRequest($request);

        if($editForm->isValid())
        {
            $this->getWorkflowManager()->update($entity);
            $this->get('session')->getFlashBag()->add('info', 'Workflow updated successfully');

            return $this->redirect($this->generateUrl('workflow_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Workflow entity.
     *
     * @Route("/{id}", name="workflow_delete")
     * @Method("DELETE")
     * @throws NotFoundHttpException
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if($form->isValid())
        {
            $entity          = $this->findWorkflow($id);

            $this->getWorkflowManager()->delete($entity);
            $this->get('session')->getFlashBag()->add('info', 'Workflow deleted successfully');
        }

        return $this->redirect($this->generateUrl('workflow'));
    }

    /**
     * Creates a form to edit a Workflow entity.
     *
     * @param WorkflowInterface $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(WorkflowInterface $entity)
    {
        $form = $this->createForm(
            new WorkflowType(),
            $entity,
            array(
                'action' => $this->generateUrl('workflow_update', array('id' => $entity->getId())),
                'method' => 'PUT',
            )
        );

        return $form;
    }

    /**
     * Creates a form to create a Workflow entity.
     *
     * @param WorkflowInterface $entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(WorkflowInterface $entity)
    {
        $form = $this->createForm(
            new WorkflowType(),
            $entity,
            array(
                'action' => $this->generateUrl('workflow_create'),
                'method' => 'POST',
            )
        );

        return $form;
    }

    /**
     * Creates a form to delete a Workflow entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('workflow_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete', 'icon' => 'trash', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }
}