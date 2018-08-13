<?php

namespace Abc\Bundle\WorkflowBundle\Workflow;

use JMS\Serializer\Annotation\Type;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class Configuration
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
     * @var null|array
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
     * @param int        $id
     * @param array|null $parameters
     * @param bool       $createDirectory Whether to create a working directory (optional, true by default)
     * @param bool       $removeDirectory Whether to remove the working directory after execution (optional, true by default)
     */
    public function __construct($id, array $parameters = null, $createDirectory = true, $removeDirectory = true)
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
     * @return array|null
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array|null $parameters
     */
    public function setParameters(array $parameters = null)
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
}