<?php

namespace Abc\Bundle\WorkflowBundle\Workflow;

use Abc\Filesystem\File;
use JMS\Serializer\SerializerBuilder;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 */
class CleanupDirectoryConfiguration extends File implements \Serializable
{
    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return SerializerBuilder::create()->build()->serialize($this, 'json');
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        /** @var CleanupDirectoryConfiguration $object */
        $object = SerializerBuilder::create()->build()->deserialize($serialized, 'Abc\Bundle\WorkflowBundle\Workflow\CleanupDirectoryConfiguration', 'json');

        $this->definition = $object->getFilesystemDefinition();
        $this->size       = $object->getSize();
        $this->path       = $object->getPath();
    }
}