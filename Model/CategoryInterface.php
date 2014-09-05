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
     * @return TaskTypeInterface The current instance
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getIcon();

    /**
     * @param string $icon
     * @return TaskTypeInterface The current instance
     */
    public function setIcon($icon);

    /**
     * @return string
     */
    public function getSlug();

    /**
     * @return ArrayCollection
     */
    public function getTypes();

    /**
     * @param TaskTypeInterface $type
     * @return TaskTypeInterface  The current instance
     */
    public function addType(TaskTypeInterface $type);

    /**
     * @param TaskTypeInterface $type
     */
    public function removeType(TaskTypeInterface $type);
}