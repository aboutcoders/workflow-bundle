<?php

namespace Abc\Bundle\WorkflowBundle\Controller;

use Abc\Bundle\WorkflowBundle\Entity\Task;
use Abc\Bundle\WorkflowBundle\Entity\Workflow;
use Abc\Bundle\WorkflowBundle\Form\TaskType;
use Abc\Bundle\WorkflowBundle\Model\TaskManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskTypeManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * Task controller.
 *
 * @Route("/task")
 */
class TaskController extends Controller
{

    /**
     * Finds and displays a Workflow tasks.
     *
     * @Route("/{id}", name="task_configure")
     * @Method("GET")
     * @Template()
     * @param $id
     * @return array
     */
    public function configureAction($id)
    {
        $workflowManager = $this->getWorkflowManager();
        $entity          = $workflowManager->findOneBy(array('id' => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Workflow entity.');
        }

        return array(
            'entity' => $entity,
        );
    }

    /**
     * Creates a new Task item entity.
     *
     * @Route("/", name="task_create")
     * @Method("POST")
     * @Template("AbcWorkflowBundle:Task:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $taskManager     = $this->getTaskManager();
        $workflowManager = $this->getWorkflowManager();

        $entity = $taskManager->create();

        $data = $request->request->get('abc_bundle_workflowbundle_task');

        if (!isset($data['typeId'])) {
            throw $this->createNotFoundException('Unable to find Task type for Task entity.');
        }

        $taskType = $this->getTaskType($data['typeId']);
        $entity->setType($taskType);

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        /** @var Workflow $workflow */
        $workflow = $workflowManager->findById($form->getData()->getWorkflowId());
        if (!$workflow) {
            throw $this->createNotFoundException('Unable to find Workflow entity.');
        }
        $entity->setWorkflow($workflow);

        if ($form->isValid()) {

            $taskManager->update($entity);
            //Update date in workflow object
            $workflow->setUpdatedAt(new \DateTime());
            $workflowManager->update($workflow);

            return $this->render('AbcWorkflowBundle:Task:editSuccess.html.twig', array('task' => $entity));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Task entity.
     *
     * @param Task $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Task $entity)
    {
        $form = $this->createForm(new TaskType($this->container), $entity, array(
            'action'     => $this->generateUrl('task_create'),
            'method'     => 'POST',
            'horizontal' => false
        ));

        return $form;
    }

    /**
     * Displays a form to edit an existing Task entity.
     *
     * @Route("/{id}/edit", name="task_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $taskManager = $this->getTaskManager();
        $entity      = $taskManager->findById($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Task entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $editForm->createView()
        );
    }

    /**
     * Creates a form to edit a Task entity.
     *
     * @param Task $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Task $entity)
    {
        $form = $this->createForm(new TaskType($this->container), $entity, array(
            'action'     => $this->generateUrl('task_update', array('id' => $entity->getId())),
            'method'     => 'PUT',
            'horizontal' => false
        ));

        return $form;
    }

    /**
     * Edits an existing Task entity.
     *
     * @Route("/{id}", name="task_update")
     * @Method("PUT")
     * @Template("AbcWorkflowBundle:Task:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $taskManager = $this->getTaskManager();
        $entity      = $taskManager->findById($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Task entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            //Workaround to update serializable parameters
            $entity->setParameters(clone($entity->getParameters()));
            $taskManager->update($entity);
            return $this->render('AbcWorkflowBundle:Task:editSuccess.html.twig', array('task' => $entity));
        }

        return array(
            'entity' => $entity,
            'form'   => $editForm->createView()
        );
    }

    /**
     * Orders a Tasks for workflow.
     *
     * @Route("/{id}/order", name="task_sort")
     * @Method("PUT")
     */
    public function sortAction(Request $request, $id)
    {
        $taskManager = $this->getTaskManager();
        $tasks       = $taskManager->findBy(array('workflowId' => $id));

        $items = $request->request->get('item');

        foreach ($tasks as $task) {
            foreach ($items as $position => $item) {
                if ($task->getId() == $item) {
                    $task->setPosition($position);
                    $taskManager->update($task);
                }
            }
        }

        //Update date in Workflow object
        $this->updateWorkflowTimestamp($id);

        return new Response('Order updated');
    }

    /**
     * Displays a form to create a new Task entity.
     *
     * @Route("/new/{id}/{type}", name="task_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($id, $type)
    {
        $taskType = $this->getTaskType($type);

        $taskManager = $this->getTaskManager();
        $entity      = $taskManager->create();
        $entity->setWorkflowId($id);
        $entity->setTypeId($type);
        $entity->setType($taskType);
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Deletes a Task entity.
     *
     * @Route("/{id}", name="task_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $taskManager = $this->getTaskManager();
        $entity      = $taskManager->findById($id);
        $workflowId  = $entity->getWorkflowId();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Task entity.');
        }

        $taskManager->delete($entity);
        //Update date in Workflow object
        $this->updateWorkflowTimestamp($workflowId);

        return new Response('Task item deleted successfully');
    }

    /**
     * @return WorkflowManagerInterface
     */
    protected function getWorkflowManager()
    {
        return $this->container->get('abc_workflow.workflow_manager');
    }

    /**
     * @return TaskManagerInterface
     */
    protected function getTaskManager()
    {
        return $this->container->get('abc_workflow.task_manager');
    }

    /**
     * @return TaskTypeManagerInterface
     */
    protected function getTaskTypeManager()
    {
        return $this->container->get('abc_workflow.task_type_manager');
    }

    /**
     * Get TaskType By Id
     *
     * @param int $id
     * @return TaskType
     */
    private function getTaskType($id)
    {
        $taskTypeManager = $this->getTaskTypeManager();
        $taskType        = $taskTypeManager->findById($id);

        if (!$taskType) {
            throw $this->createNotFoundException('Unable to find TaskType entity.');
        }
        return $taskType;
    }

    /**
     * Updates updated date in Workflow object
     *
     * @param $id
     */
    private function updateWorkflowTimestamp($id)
    {
        $workflowManager = $this->getWorkflowManager();
        $workflow        = $workflowManager->findById($id);
        $workflow->setUpdatedAt(new \DateTime());
        $workflowManager->update($workflow);
    }


}
