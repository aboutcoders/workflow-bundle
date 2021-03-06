<?php

namespace Abc\Bundle\WorkflowBundle\Controller;

use Abc\Bundle\JobBundle\Job\Report\ReportInterface;
use Abc\Bundle\JobBundle\Job\Status;
use Abc\Bundle\WorkflowBundle\Entity\Workflow;
use Abc\Bundle\WorkflowBundle\Doctrine\WorkflowManager;
use Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\WorkflowInterface;
use Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface;
use Abc\Bundle\WorkflowBundle\Workflow\ManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Execution controller.
 *
 * @Route("/workflow-execution")
 */
class ExecutionController extends BaseController
{

    /**
     * Executes a Workflow.
     *
     * @Route("/{id}/execute", name="workflow_execute")
     * @Method("GET")
     * @Template()
     */
    public function executeAction($id)
    {
        $workflow = $this->findWorkflow($id);

        if ($workflow->isDisabled()) {
            $this->get('session')->getFlashBag()->add('danger', 'Workflow is Disabled');

            return $this->redirect($this->generateUrl('workflow_show', array('id' => $workflow->getId())));
        }

        $execution = $this->getManager()->execute($id);

        $this->get('session')->getFlashBag()->add('info', 'Workflow execution triggered (#' . $execution->getExecutionNumber() . '). Check workflow history for details');

        return $this->redirect($this->generateUrl('workflow_show', array('id' => $execution->getWorkflow()->getId())));
    }

    /**
     * Cancel workflow execution.
     *
     * @Route("/{id}/cancel", name="workflow_cancel_execution")
     * @Method("GET")
     * @Template()
     */
    public function cancelAction($id)
    {
        $execution = $this->getExecutionManager()->findById($id);

        if (!$execution) {
            throw $this->createNotFoundException('Unable to find execution');
        }

        $this->getManager()->cancel($execution->getTicket());

        $this->get('session')->getFlashBag()->add('info', 'Workflow execution #' . $execution->getExecutionNumber() . ' cancelled');

        return $this->redirect($this->generateUrl('workflow_show', array('id' => $execution->getWorkflow()->getId())));
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
        $entity = $this->findExecution($id);

        $report     = $this->getManager()->getReport($entity->getTicket());
        $progress   = $this->getManager()->getProgress($entity->getTicket());

        return array(
            'entity'   => $entity,
            'progress' => $progress,
            'report'   => $report
        );
    }

    /**
     * Status of a Workflow execution.
     *
     * @Route("/{id}/execution-status", name="workflow_execution_status")
     * @Method("GET")
     */
    public function statusAction($id)
    {
        $entity = $this->findExecution($id);

        $progress = $this->getManager()->getProgress($entity->getTicket());

        $response = new Response(json_encode(
                array(
                    'progress' => $progress,
                    'message'  => 'Processing'
                ))
        );
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Workflow execution history.
     *
     * @Route("/{id}/history", name="execution_history")
     * @Method("GET")
     * @Template()
     */
    public function historyAction($id)
    {
        return array(
            'entity' => $this->findWorkflow($id)
        );
    }
}