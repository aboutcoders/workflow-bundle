<?php

namespace Abc\Bundle\WorkflowBundle\Controller;

use Abc\Bundle\JobBundle\Job\ManagerInterface;
use Abc\Bundle\JobBundle\Job\Report\ReportInterface;
use Abc\Bundle\JobBundle\Job\Status;
use Abc\Bundle\WorkflowBundle\Doctrine\WorkflowManager;
use Abc\Bundle\WorkflowBundle\Entity\Workflow;
use Abc\Bundle\WorkflowBundle\Form\WorkflowType;
use Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface;
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

        if($workflow->isDisabled())
        {
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
        $entity = $this->findExecution($id);

        /** @var ManagerInterface $jobManager */
        $jobManager = $this->get('abc.job.manager');
        $report     = $jobManager->getReport($entity->getTicket());

        $progress = 0;
        if($report->getStatus() == Status::PROCESSED())
        {
            $progress = 100;
        }

        return array(
            'entity' => $entity,
            'progress' => $progress,
            'report' => $report
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
        return array(
            'entity' => $this->findWorkflow($id)
        );
    }
}