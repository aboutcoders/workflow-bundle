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
     * @return Category
     */
    public function getCategory();

    /**
     * @param CategoryInterface $category
     * @return void
     */
    public function setCategory(CategoryInterface $category);
}