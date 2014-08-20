<?php

namespace Abc\Bundle\WorkflowBundle\Workflow;

use JMS\Serializer\Annotation\Type;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class Response
{
    /**
     * @var array
     * @Type("array<string>")
     */
    protected $actions = array();

    /**
     * @param string $name
     * @return void
     * @throws \InvalidArgumentException If $name is not a string
     */
    public function addAction($name)
    {
        if(!is_string($name))
        {
            throw new \InvalidArgumentException('$name must be a string');
        }

        $this->actions[] = (string)$name;
    }

    /**
     * @return array Array containing strings
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param array $actions
     * @return void
     * @throws \InvalidArgumentException If array contain an element that is not of type string
     */
    public function setActions(array $actions)
    {
        foreach($actions as $name)
        {
            $this->addAction($name);
        }
    }

    /**
     * @param $name
     * @return void
     * @throws \InvalidArgumentException If $name is not a string
     */
    public function removeAction($name)
    {
        if(!is_string($name))
        {
            throw new \InvalidArgumentException('$name must be a string');
        }

        if(false !== $pos = array_search((string)$name, $this->actions))
        {
            unset($this->actions[$pos]);
        }
    }


}