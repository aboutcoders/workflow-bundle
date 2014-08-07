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
    protected $jobType;

    /**
     * @var string
     */
    protected $formServiceName;

    /**
     * @var TaskTypeCategoryInterface
     */
    protected $category;

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
    public function setJobType($jobType)
    {
        $this->jobType = $jobType;
    }

    /**
     * {@inheritdoc}
     */
    public function getJobType()
    {
        return $this->jobType;
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
    public function setFormServiceName($formServiceName = null)
    {
        $this->formServiceName = $formServiceName;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * {@inheritdoc}
     */
    public function setCategory(TaskTypeCategoryInterface $category)
    {
        $this->category = $category;
    }

    /**
     * {@inheritdoc}
     */
    function __toString()
    {
        return $this->name;
    }
}