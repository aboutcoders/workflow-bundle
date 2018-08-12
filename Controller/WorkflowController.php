<?php

namespace Abc\Bundle\WorkflowBundle\Controller;

use Abc\Bundle\WorkflowBundle\Entity\Workflow;
use Abc\Bundle\WorkflowBundle\Model\WorkflowInterface;
use Abc\Bundle\WorkflowBundle\Model\WorkflowList;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations as FOS;
use FOS\RestBundle\Request\ParamFetcherInterface;
use HttpException;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Workflow controller.
 *
 * @FOS\RouteResource("Workflow")
 */
class WorkflowController extends BaseController
{
    /**
     * Returns a list of workflows
     *
     * @FOS\QueryParam(name="page", requirements="\d+", default="1", description="Page number of the result set")
     * @FOS\QueryParam(name="limit", requirements="\d+", default="10", description="Page size")
     * @FOS\QueryParam(name="sortCol", default="createdAt", description="Sort columns, valid values are [name|updatedAt|createdAt]")
     * @FOS\QueryParam(name="sortDir", default="DESC", description="Sort direction, valid values are [ASC|DESC]")
     * @FOS\QueryParam(name="criteria", description="Search criteria defined as array, valid array keys are [name|updatedAt|createdAt]")
     *
     * @Operation(
     *     tags={"WorkflowBundle"},
     *     summary="Returns a list of workflows",
     *     @SWG\Parameter(
     *         name="sortDir",
     *         in="query",
     *         description="Sort direction",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number of the result set",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Page size",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="sortCol",
     *         in="query",
     *         description="Sort column",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="criteria",
     *         in="query",
     *         description="Searching criteria",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when request is invalid"
     *     )
     * )
     *
     * @param ParamFetcherInterface $paramFetcher
     * @return WorkflowList
     * @throws HttpException
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $page       = $paramFetcher->get('page');
        $sortColumn = $paramFetcher->get('sortCol');
        $sortDir    = $paramFetcher->get('sortDir');
        $limit      = $paramFetcher->get('limit');
        $page       = (int)$page - 1;
        $offset     = ($page > 0) ? ($page) * $limit : 0;
        $criteria   = $paramFetcher->get('criteria');

        if (!$criteria) {
            $criteria = [];
        }

        $criteria = $this->filterCriteria($criteria);

        $entities = $this->getWorkflowManager()->findBy($criteria, [$sortColumn => $sortDir], $limit, $offset);
        $count    = $this->getWorkflowManager()->findByCount($criteria);

        $list = new WorkflowList();
        $list->setItems($entities);
        $list->setTotalCount($count);

        return $list;
    }

    /**
     *
     * @Operation(
     *     tags={"WorkflowBundle"},
     *     summary="Returns a Workflow",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @Model(type="Abc\Bundle\WorkflowBundle\Model\Workflow")
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when entity not found"
     *     )
     * )
     *
     * @param $id
     * @return WorkflowInterface
     */
    public function getAction($id)
    {
        $workflow = $this->getWorkflowManager()->findById($id);
        if (null == $workflow) {
            $this->createNotFoundException(sprintf('Video with id "%s" not found.', $id));
        }

        return $workflow;
    }

    /**
     * Adds a new Workflow.
     *
     * @Operation(
     *     tags={"WorkflowBundle"},
     *     summary="Adds a new Workflow",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @Model(type="Abc\Bundle\WorkflowBundle\Model\Workflow")
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Validation error"
     *     )
     * )
     *
     * @ParamConverter("workflow", converter="fos_rest.request_body")
     * @param Workflow $workflow
     * @return Response|WorkflowInterface
     * @Post("/workflows")
     */
    public function postAction(Workflow $workflow)
    {

        $validator = $this->get('validator');
        $errors    = $validator->validate($workflow);

        if (count($errors) > 0) {
            $formattedErrors = [];
            foreach ($errors as $error) {
                $formattedErrors[$error->getPropertyPath()] = $error->getMessage();
            }

            return new JsonResponse($formattedErrors, 400);
        }

        $this->getWorkflowManager()->save($workflow);

        return $workflow;
    }

    /**
     * Updates a new Workflow.
     *
     * @Operation(
     *     tags={"WorkflowBundle"},
     *     summary="Updates a Workflow",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @Model(type="Abc\Bundle\WorkflowBundle\Model\Workflow")
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Validation error"
     *     )
     * )
     *
     *
     * @ParamConverter("workflow", converter="fos_rest.request_body")
     * @param string   $id
     * @param Workflow $workflow
     * @return Response|WorkflowInterface
     * @Put("/workflows/{id}")
     */
    public function putAction($id, Workflow $workflow)
    {
        $entity    = $this->getWorkflowManager()->findById($id);
        $validator = $this->get('validator');
        $workflow->setCreatedAt($entity->getCreatedAt());
        $this->getWorkflowManager()->save($workflow);

        return $workflow;
    }

    /**
     * Delete a Workflow.
     *
     *
     * @Operation(
     *     tags={"WorkflowBundle"},
     *     summary="Deletes a Workflow",
     *     @SWG\Response(
     *         response="204",
     *         description="Returned when successful",
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Form validation error"
     *     )
     * )
     *
     * @return Response
     */
    public function deleteAction($id)
    {

        $workflow = $this->getWorkflowManager()->findById($id);
        if (null == $workflow) {
            $this->createNotFoundException(sprintf('Workflow with id "%s" not found.', $id));
        }

        $this->getWorkflowManager()->delete($workflow);

        return new Response('', 204);
    }
}