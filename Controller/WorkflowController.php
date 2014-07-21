<?php

namespace Abc\Bundle\WorkflowBundle\Controller;

use Abc\Bundle\JobBundle\Api\Manager;
use Abc\Bundle\JobBundle\Api\Status;
use Abc\Bundle\JobBundle\Doctrine\JobManager;
use Abc\Bundle\JobBundle\Job\Report;
use Abc\Bundle\WorkflowBundle\Doctrine\WorkflowManager;
use Abc\Bundle\WorkflowBundle\Entity\Workflow;
use Abc\Bundle\WorkflowBundle\Form\WorkflowType;
use Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Workflow controller.
 *
 * @Route("/workflow")
 */
class WorkflowController extends Controller
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
        $workflowManager = $this->getWorkflowManager();
        $entities        = $workflowManager->findAll();

        return array(
            'entities' => $entities,
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

        $entity = $workflowManager->create();

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $workflowManager->update($entity);

            return $this->redirect($this->generateUrl('workflow_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Workflow entity.
     *
     * @param Workflow $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Workflow $entity)
    {
        $form = $this->createForm(new WorkflowType(), $entity, array(
            'action' => $this->generateUrl('workflow_create'),
            'method' => 'POST',
        ));

        return $form;
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
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Workflow entity.
     *
     * @Route("/{id}", name="workflow_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $workflowManager = $this->getWorkflowManager();
        $entity          = $workflowManager->findById($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Workflow entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Executes a Workflow.
     *
     * @Route("/{id}/execute", name="workflow_execute")
     * @Method("GET")
     * @Template()
     */
    public function executeAction($id)
    {
        $workflowManager = $this->getWorkflowManager();
        $workflow        = $workflowManager->findById($id);

        if (!$workflow) {
            throw $this->createNotFoundException('Unable to find Workflow entity.');
        }

        if ($workflow->isDisabled()) {
            $this->get('session')->getFlashBag()->add('danger', 'Workflow is Disabled');
            return $this->redirect($this->generateUrl('workflow_show', array('id' => $workflow->getId())));
        }

        $executionManager = $this->getExecutionManager();

        /** @var Manager $manager */
        $jobManager = $this->get('abc.job.manager');

        $execution = $executionManager->execute($workflow, $jobManager);

        return $this->redirect($this->generateUrl('workflow_execution', array('id' => $execution->getId())));
    }

    /**
     * Execution of a Workflow.
     *
     * @Route("/{id}/execution", name="workflow_execution")
     * @Method("GET")
     * @Template()
     */
    public function executionAction($id)
    {
        $executionManager = $this->getExecutionManager();
        $entity                   = $executionManager->findById($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find execution entity.');
        }

        /** @var Manager $manager */
        $jobManager = $this->get('abc.job.manager');

        /** @var Report $report */
        $report = $jobManager->getReport($entity->getTicket());

        $progress = 0;
        if ($report->getStatus() == Status::PROCESSED()) {
            $progress = 100;
        }

        return array(
            'entity'   => $entity,
            'progress' => $progress,
            'report'   => $report
        );
    }


    /**
     * Workflow history.
     *
     * @Route("/{id}/history", name="workflow_history")
     * @Method("GET")
     * @Template()
     */
    public function historyAction($id)
    {
        $workflowManager = $this->getWorkflowManager();
        $entity          = $workflowManager->findById($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find execution entity.');
        }

        $executionManager = $this->getExecutionManager();
        $executions               = $executionManager->findBy(
            array('workflowId' => $id),
            array('createdAt' => 'DESC')
        );

        return array(
            'entity'     => $entity,
            'executions' => $executions
        );
    }

    /**
     * Displays a form to edit an existing Workflow entity.
     *
     * @Route("/{id}/edit", name="workflow_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $workflowManager = $this->getWorkflowManager();
        $entity          = $workflowManager->findById($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Workflow entity.');
        }

        $editForm   = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Workflow entity.
     *
     * @param Workflow $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Workflow $entity)
    {
        $form = $this->createForm(new WorkflowType(), $entity, array(
            'action' => $this->generateUrl('workflow_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing Workflow entity.
     *
     * @Route("/{id}", name="workflow_update")
     * @Method("PUT")
     * @Template("AbcWorkflowBundle:Workflow:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $workflowManager = $this->getWorkflowManager();
        $entity          = $workflowManager->findById($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Workflow entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm   = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $workflowManager->update($entity);
            $this->get('session')->getFlashBag()->add('info', 'Workflow updated successfully');
            return $this->redirect($this->generateUrl('workflow_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Deletes a Workflow entity.
     *
     * @Route("/{id}", name="workflow_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $workflowManager = $this->getWorkflowManager();
            $entity          = $workflowManager->findById($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Workflow entity.');
            }

            $workflowManager->delete($entity);
            $this->get('session')->getFlashBag()->add('info', 'Workflow deleted successfully');
        }

        return $this->redirect($this->generateUrl('workflow'));
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

    /**
     * @return WorkflowManager
     */
    protected function getWorkflowManager()
    {
        return $this->container->get('abc.workflow.workflow_manager');
    }

    /**
     * @return ExecutionManagerInterface
     */
    private function getExecutionManager()
    {
        return $this->container->get('abc.workflow.execution_manager');
    }

}
