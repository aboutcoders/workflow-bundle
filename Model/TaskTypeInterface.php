<?php

namespace Abc\Bundle\WorkflowBundle\Model;

interface TaskTypeInterface
{
    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set name
     *
     * @param string $name
     * @return TaskType
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();


    /**
     * @return string
     */
    public function getFormServiceName();

    /**
     * @param string $formServiceName
     */
    public function setFormServiceName($formServiceName);
}