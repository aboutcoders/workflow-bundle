<?php

namespace Abc\Bundle\WorkflowBundle\Model;

/**
 * TaskTypeCategory
 */
class TaskTypeCategory implements TaskTypeCategoryInterface
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
    function __toString()
    {
        return $this->name;
    }
}