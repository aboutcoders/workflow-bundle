<?php

namespace Abc\Bundle\WorkflowBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

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
     * @var string
     */
    protected $icon = "";

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var ArrayCollection
     */
    protected $types;

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
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return $this->icon;
    }


    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * {@inheritDoc}
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * {@inheritDoc}
     */
    public function addType(TaskTypeInterface $type)
    {
        $this->types[] = $type;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function removeType(TaskTypeInterface $type)
    {
        $this->types->removeElement($type);
    }


    /**
     * {@inheritdoc}
     */
    function __toString()
    {
        return $this->name;
    }
}