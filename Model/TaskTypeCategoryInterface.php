<?php

namespace Abc\Bundle\WorkflowBundle\Model;

interface TaskTypeCategoryInterface
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
    public function getSlug();
}