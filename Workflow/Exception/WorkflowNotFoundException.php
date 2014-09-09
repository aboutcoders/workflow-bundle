<?php

namespace Abc\Bundle\WorkflowBundle\Workflow\Exception;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class WorkflowNotFoundException extends \Exception
{
    /** @var mixed */
    protected $id;

    /**
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = $id;

        parent::__construct(sprintf('Workflow with id "%s" not found', $id), 404);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}