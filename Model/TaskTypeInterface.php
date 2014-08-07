<?php

namespace Abc\Bundle\WorkflowBundle\Model;

interface TaskTypeInterface
{
    /**
     * @return integer
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return TaskType
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getIcon();

    /**
     * @param string $icon
     * @return TaskType
     */
    public function setIcon($icon);

    /**
     * @return string
     */
    public function getJobType();

    /**
     * @param string $jobType
     * @return void
     */
    public function setJobType($jobType);

    /**
     * @return string|null
     */
    public function getFormServiceName();

    /**
     * @param string|null $formServiceName
     * @return void
     */
    public function setFormServiceName($formServiceName = null);

    /**
     * @return TaskTypeCategory
     */
    public function getCategory();

    /**
     * @param TaskTypeCategoryInterface $category
     * @return void
     */
    public function setCategory(TaskTypeCategoryInterface $category);
}