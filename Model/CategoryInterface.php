<?php

namespace Abc\Bundle\WorkflowBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

interface CategoryInterface
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
    public function getSlug();

    /**
     * Get all types
     *
     * @return ArrayCollection
     */
    public function getTypes();

    /**
     * Add Task
     *
     * @param TaskTypeInterface $type
     */
    public function addType(TaskTypeInterface $type);

    /**
     * Remove Types
     *
     * @param TaskTypeInterface $type
     */
    public function removeType(TaskTypeInterface $type);
}