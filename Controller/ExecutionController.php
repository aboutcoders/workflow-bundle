<?php

namespace Abc\Bundle\WorkflowBundle\Controller;

use Abc\Bundle\JobBundle\Job\ManagerInterface;
use Abc\Bundle\JobBundle\Job\Report;
use Abc\Bundle\JobBundle\Job\Status;
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
 * Execution controller.
 *
 * @Route("/workflow-execution")
 */
class ExecutionController extends Controller
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
        $workflowManager = $this->getWorkflowManager();
        $workflow        = $workflowManager->findById($id);

        if (!$workflow) {
            throw $this->createNotFoundException('Unable to find Workflow entity.');
        }

        if ($workflow->isDisabled()) {
            $this->get('session')->getFlashBag()->add('danger', 'Workflow is Disabled');
            return $this->redirect($this->generateUrl('workflow_show', array('id' => $workflow->getId())));
        }

        /** @var ManagerInterface $manager */
        $manager = $this->get('abc.job.manager');

        $ticket = $manager->addJob('workflow', $workflow);

        $execution = $this->getExecutionManager()->create();
        $execution->setWorkflow($workflow);
        $execution->setTicket($ticket);

        $this->getExecutionManager()->update($execution);

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
        $entity           = $executionManager->findById($id);

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
     * Workflow execution history.
     *
     * @Route("/{id}/history", name="execution_history")
     * @Method("GET")
     * @Template()
     */
    public function historyAction($id)
    {
        $workflowManager = $this->getWorkflowManager();
        $entity          = $workflowManager->findById($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find workflow entity.');
        }

        return array(
            'entity' => $entity
        );
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
