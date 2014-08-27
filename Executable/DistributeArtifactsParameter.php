<?php

namespace Abc\Bundle\WorkflowBundle\Executable;

class DistributeArtifactsParameter implements \Serializable
{
    /**
     * @var int
     */
    protected $definitionId;

    /**
     * Remove workspace file after X days
     *
     * @var int
     */
    protected $workspaceLifetime = 15;


    /**
     * Filesystem
     *
     * @return string
     */
    public function getDefinitionId()
    {
        return $this->definitionId;
    }

    /**
     * @param string $definitionId
     */
    public function setDefinitionId($definitionId)
    {
        $this->definitionId = $definitionId;
    }

    /**
     * @return int
     */
    public function getWorkspaceLifetime()
    {
        return $this->workspaceLifetime;
    }

    /**
     * @param int $workspaceLifetime
     */
    public function setWorkspaceLifetime($workspaceLifetime)
    {
        $this->workspaceLifetime = $workspaceLifetime;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array(
            'definitionId'      => $this->definitionId,
            'workspaceLifetime' => $this->workspaceLifetime,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data                    = unserialize($serialized);
        $this->definitionId      = $data['definitionId'];
        $this->workspaceLifetime = $data['workspaceLifetime'];
    }
}