<?php

namespace Abc\Bundle\WorkflowBundle\Controller;

use Abc\Bundle\WorkflowBundle\Entity\Task;
use Abc\Bundle\WorkflowBundle\Form\TaskType;
use Abc\Bundle\WorkflowBundle\Model\TaskInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskTypeInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskTypeManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\WorkflowInterface;
use Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Task controller.
 *
 * @Route("/task")
 */
class TaskController extends BaseController
{

    /**
     * Finds and displays a Workflow tasks.
     *
     * @Route("/{id}", name="task_configure")
     * @Method("GET")
     * @Template()
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function configureAction($id)
    {
        return array(
            'entity' => $this->findWorkflow($id)
        );
    }

    /**
     * Creates a new Task entity.
     *
     * @Route("/", name="task_create")
     * @Method("POST")
     * @Template("AbcWorkflowBundle:Task:new.html.twig")
     * @throws NotFoundHttpException
     */
    public function createAction(Request $request)
    {
        $taskManager     = $this->getTaskManager();

        $entity = $taskManager->create();

        $data = $request->request->get('abc_bundle_workflowbundle_task');

        if(!isset($data['typeId']))
        {
            throw $this->createNotFoundException('Unable to find task type for task');
        }

        $taskType = $this->findTaskType($data['typeId']);
        $entity->setType($taskType);

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $workflow = $this->findWorkflow($form->getData()->getWorkflowId());
        $entity->setWorkflow($workflow);

        if($form->isValid())
        {

            $taskManager->update($entity);
            $this->updateWorkflowTimestamp($workflow);

            return $this->render('AbcWorkflowBundle:Task:editSuccess.html.twig', array('task' => $entity));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Task entity.
     *
     * @Route("/{id}/edit", name="task_edit")
     * @Method("GET")
     * @Template()
     * @throws NotFoundHttpException
     */
    public function editAction($id)
    {
        $entity = $this->findTask($id);

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'form' => $editForm->createView()
        );
    }

    /**
     * Edits an existing Task entity.
     *
     * @Route("/{id}", name="task_update")
     * @Method("PUT")
     * @Template("AbcWorkflowBundle:Task:edit.html.twig")
     * @throws NotFoundHttpException
     */
    public function updateAction(Request $request, $id)
    {
        $taskManager = $this->getTaskManager();
        $entity      = $this->findTask($id);
        $editForm    = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if($editForm->isValid())
        {
            $taskManager->update($entity);

            return $this->render('AbcWorkflowBundle:Task:editSuccess.html.twig', array('task' => $entity));
        }

        return array(
            'entity' => $entity,
            'form' => $editForm->createView()
        );
    }

    /**
     * Orders tasks within a workflow
     *
     * @Route("/{id}/order", name="task_sort")
     * @Method("PUT")
     * @throws NotFoundHttpException
     */
    public function sortAction(Request $request, $id)
    {
        $taskManager = $this->getTaskManager();
        $tasks       = $taskManager->findBy(array('workflowId' => $id));

        $items = $request->request->get('item');

        foreach($tasks as $task)
        {
            foreach($items as $position => $item)
            {
                /** @var TaskInterface $task */
                if($task->getId() == $item)
                {
                    $task->setPosition($position);
                    $taskManager->update($task);
                }
            }
        }

        // Update date in Workflow object
        $this->updateWorkflowTimestamp($this->getWorkflowManager()->findById($id));

        return new Response('Order updated');
    }

    /**
     * Displays a form to create a new Task entity.
     *
     * @Route("/new/{id}/{type}", name="task_new")
     * @Method("GET")
     * @Template()
     * @throws NotFoundHttpException
     */
    public function newAction($id, $type)
    {
        $taskType = $this->findTaskType($type);
        $workflow = $this->findWorkflow($id);

        $task = $this->getTaskManager()->create();
        $task->setWorkflow($workflow);
        $task->setType($taskType);

        $form = $this->createCreateForm($task);

        return array(
            'entity' => $task,
            'form' => $form->createView(),
        );
    }

    /**
     * Deletes a Task entity.
     *
     * @Route("/{id}", name="task_delete")
     * @Method("DELETE")
     * @throws NotFoundHttpException
     */
    public function deleteAction(Request $request, $id)
    {
        $entity = $this->findTask($id);

        $this->getTaskManager()->delete($entity);

        $this->updateWorkflowTimestamp($entity->getWorkflow());

        return new Response('Task item deleted successfully');
    }

    /**
     * Creates a form to create a Task entity.
     *
     * @param TaskInterface $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TaskInterface $entity)
    {
        $form = $this->createForm(
            new TaskType($this->container),
            $entity,
            array(
                'action' => $this->generateUrl('task_create'),
                'method' => 'POST',
                'horizontal' => false
            )
        );

        return $form;
    }

    /**
     * Creates a form to edit a Task entity.
     *
     * @param TaskInterface $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(TaskInterface $entity)
    {
        $form = $this->createForm(
            new TaskType($this->container),
            $entity,
            array(
                'action' => $this->generateUrl('task_update', array('id' => $entity->getId())),
                'method' => 'PUT',
                'horizontal' => false
            )
        );

        return $form;
    }

    /**
     * Updates updated date in Workflow object
     *
     * @param WorkflowInterface $workflow
     */
    private function updateWorkflowTimestamp(WorkflowInterface $workflow)
    {
        $workflow->setUpdatedAt(new \DateTime());
        $this->getWorkflowManager()->update($workflow);
    }
}