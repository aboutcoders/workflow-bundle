<?php

namespace Abc\Bundle\WorkflowBundle\Workflow;

use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Annotation\Type;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class Configuration implements \Serializable
{
    /**
     * @var int
     * @Type("integer")
     */
    private $id;
    /**
     * @var int
     * @Type("integer")
     */
    private $index = 0;

    /**
     * @var null|\Serializable
     * @Type("string")
     */
    private $parameters;

    /**
     * @var boolean
     * @Type("boolean")
     */
    private $createDirectory;

    /**
     * @var boolean
     * @Type("boolean")
     */
    private $removeDirectory;

    private $serializedParameters;

    /**
     * @param int                $id
     * @param \Serializable|null $parameters
     * @param bool               $createDirectory Whether to create a working directory (optional, true by default)
     * @param bool               $removeDirectory Whether to remove the working directory after execution (optional, true by default)
     */
    public function __construct($id, \Serializable $parameters = null, $createDirectory = true, $removeDirectory = true)
    {
        $this->id              = $id;
        $this->index           = 0;
        $this->parameters      = $parameters;
        $this->createDirectory = $createDirectory;
        $this->removeDirectory = $removeDirectory;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param int $index
     * @return void
     */
    public function setIndex($index)
    {
        $this->index = $index;
    }

    /**
     * @return \Serializable|null
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param \Serializable|null $parameters
     */
    public function setParameters(\Serializable $parameters = null)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param boolean $createDirectory
     */
    public function setCreateDirectory($createDirectory)
    {
        $this->createDirectory = $createDirectory;
    }

    /**
     * @return boolean
     */
    public function getCreateDirectory()
    {
        return $this->createDirectory;
    }

    /**
     * @param boolean $removeDirectory
     */
    public function setRemoveDirectory($removeDirectory)
    {
        $this->removeDirectory = $removeDirectory;
    }

    /**
     * @return boolean
     */
    public function getRemoveDirectory()
    {
        return $this->removeDirectory;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        $tmp = $this->parameters;
        $this->parameters = serialize($this->parameters);

        try
        {
            $data = SerializerBuilder::create()->build()->serialize($this, 'json');

            $this->parameters = $tmp;

            return $data;
        }
        catch(\Exception $e)
        {
            $this->parameters = $tmp;

            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        /** @var Configuration $object */
        $object = SerializerBuilder::create()->build()->deserialize($serialized, 'Abc\Bundle\WorkflowBundle\Workflow\Configuration', 'json');

        $this->id = $object->getId();
        $this->index = $object->getIndex();
        $this->parameters = unserialize($object->getParameters());
        $this->createDirectory = $object->getCreateDirectory();
        $this->removeDirectory = $object->getRemoveDirectory();
    }
}