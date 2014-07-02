<?php

namespace Abc\Bundle\WorkflowBundle\Model;

/**
 * TaskType
 */
class TaskType implements TaskTypeInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $formServiceName;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormServiceName()
    {
        return $this->formServiceName;
    }

    /**
     * {@inheritdoc}
     */
    public function setFormServiceName($formServiceName)
    {
        $this->formServiceName = $formServiceName;
    }

    function __toString()
    {
        return $this->name;
    }
}
