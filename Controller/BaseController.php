<?php

namespace Abc\Bundle\WorkflowBundle\Controller;

use Abc\Bundle\WorkflowBundle\Model\ExecutionInterface;
use Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskTypeInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskTypeManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\WorkflowInterface;
use Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface;
use Abc\Bundle\WorkflowBundle\Workflow\ManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BaseController extends FOSRestController
{

    /**
     * @param $criteria
     * @return array
     * @throws HttpException
     */
    protected function filterCriteria($criteria)
    {
        if (!is_array($criteria)) {
            throw new \HttpException(400, 'Invalid value for parameter criteria');
        }

        return $criteria;
    }

    /**
     * @param $id
     * @return ExecutionInterface
     * @throws NotFoundHttpException
     */
    protected function findExecution($id)
    {
        if($entity = $this->getExecutionManager()->findById($id))
        {
            return $entity;
        }

        throw $this->createNotFoundException(sprintf('Unable to find execution with id "%s"', $id));
    }

    /**
     * @param $id
     * @return TaskInterface
     * @throws NotFoundHttpException
     */
    protected function findTask($id)
    {
        if($task = $this->getTaskManager()->findById($id))
        {
            return $task;
        }

        throw $this->createNotFoundException(sprintf('Unable to find task with id "%s"', $id));
    }

    /**
     * @param int $id
     * @return TaskTypeInterface
     * @throws NotFoundHttpException
     */
    protected function findTaskType($id)
    {
        if($taskType = $this->getTaskTypeManager()->findById($id))
        {
            return $taskType;
        }

        throw $this->createNotFoundException(sprintf('Unable to find task type with id "%s"', $id));
    }

    /**
     * @param int $id
     * @return WorkflowInterface
     * @throws NotFoundHttpException
     */
    protected function findWorkflow($id)
    {
        if($workflow = $this->getWorkflowManager()->findById($id))
        {
            return $workflow;
        }

        throw $this->createNotFoundException(sprintf('Unable to find workflow with id "%s"', $id));
    }

    /**
     * @return ManagerInterface
     */
    public function getManager()
    {
        return $this->get('abc.workflow.manager');
    }

    /**
     * @return ExecutionManagerInterface
     */
    protected function getExecutionManager()
    {
        return $this->container->get('abc.workflow.execution_manager');
    }

    /**
     * @return TaskManagerInterface
     */
    protected function getTaskManager()
    {
        return $this->container->get('abc.workflow.task_manager');
    }

    /**
     * @return TaskTypeManagerInterface
     */
    protected function getTaskTypeManager()
    {
        return $this->container->get('abc.workflow.task_type_manager');
    }

    /**
     * @return WorkflowManagerInterface
     */
    protected function getWorkflowManager()
    {
        return $this->container->get('abc.workflow.workflow_manager');
    }
} 