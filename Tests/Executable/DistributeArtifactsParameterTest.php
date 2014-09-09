<?php


namespace Abc\Bundle\WorkflowBundle\Tests\Executable;


use Abc\Bundle\WorkflowBundle\Executable\DistributeArtifactsParameter;

class DistributeArtifactsParameterTest extends \PHPUnit_Framework_TestCase
{
    public function testSerializable()
    {
        $subject = new DistributeArtifactsParameter();
        $subject->setDefinitionId(1);
        $subject->setWorkspaceLifetime(1);

        $data = serialize($subject);

        $object = unserialize($data);

        $this->assertEquals($subject, $object);
    }
}