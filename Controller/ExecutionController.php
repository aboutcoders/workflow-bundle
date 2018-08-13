<?php

namespace Abc\Bundle\WorkflowBundle\Controller;

use Abc\Bundle\JobBundle\Job\Report\ReportInterface;
use Abc\Bundle\WorkflowBundle\Model\WorkflowInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations as FOS;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * Execution controller.
 *
 * @FOS\RouteResource("Execute")
 */
class ExecutionController extends BaseController
{

    /**
     * Execute a Workflow.
     *
     * @Operation(
     *     tags={"WorkflowBundle"},
     *     summary="Triggers a Workflow execution",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @Model(type="Abc\Bundle\WorkflowBundle\Model\Execution")
     *     )
     * )
     *
     * @param int $id
     * @return \Abc\Bundle\WorkflowBundle\Model\ExecutionInterface
     * @throws \Abc\Bundle\WorkflowBundle\Workflow\Exception\WorkflowNotFoundException
     */
    public function postAction($id)
    {
        $workflow = $this->findWorkflow($id);

        $execution = $this->getManager()->execute($id);

        return $execution;
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
     * Get Workflow execution.
     *
     * @Operation(
     *     tags={"WorkflowBundle"},
     *     summary="Get Workflow execution details",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     * @param $id
     * @return array
     * @throws \Abc\Bundle\JobBundle\Job\Exception\TicketNotFoundException
     */
    public function getAction($id)
    {
        $entity = $this->findExecution($id);

        $report   = $this->getManager()->getReport($entity->getTicket());
        $progress = $this->getManager()->getProgress($entity->getTicket());

        return [
            'entity'   => $entity,
            'progress' => $progress,
            'report'   => $report
        ];
    }

    /**
     * Status of a Workflow execution.
     *
     * @Operation(
     *     tags={"WorkflowBundle"},
     *     summary="Get Status of a Workflow execution",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     * @param $id
     * @return Response
     */
    public function statusAction($id)
    {
        $entity = $this->findExecution($id);

        $progress = $this->getManager()->getProgress($entity->getTicket());

        $response = new JsonResponse(
            [
                'progress' => $progress,
                'message'  => 'Processing'
            ]
        );
        return $response;
    }


    /**
     * Get Workflow execution history
     *
     * @Operation(
     *     tags={"WorkflowBundle"},
     *     summary="Get Workflow execution history",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @Model(type="Abc\Bundle\WorkflowBundle\Model\Workflow")
     *     )
     * )
     *
     * @param int $id
     * @return Response|WorkflowInterface
     */
    public function historyAction($id)
    {
        $workflow = $this->findWorkflow($id);

        return $workflow;
    }
}