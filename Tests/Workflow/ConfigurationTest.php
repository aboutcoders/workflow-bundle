<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Workflow;

use Abc\Bundle\WorkflowBundle\Workflow\Configuration;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @param      $id
     * @param null $parameters
     * @param null $createDirectory
     * @param null $removeDirectory
     * @dataProvider getConstructorArgs
     */
    public function testConstruct($id, $parameters = null, $createDirectory = null, $removeDirectory = null)
    {
        if($createDirectory == null && $removeDirectory == null)
        {
            $subject = new Configuration($id, $parameters);

            $this->assertTrue($subject->getCreateDirectory());
        }
        elseif($removeDirectory == null)
        {
            $subject = new Configuration($id, $parameters, $createDirectory);

            $this->assertTrue($subject->getCreateDirectory());
            $this->assertTrue($subject->getRemoveDirectory());
        }
        else
        {
            $subject = new Configuration($id, $parameters, $createDirectory, $removeDirectory);

            $this->assertEquals($createDirectory, $subject->getCreateDirectory());
            $this->assertEquals($removeDirectory, $subject->getRemoveDirectory());
        }

        $this->assertEquals($id, $subject->getId());
        $this->assertEquals($parameters, $subject->getParameters());
    }


    public function getConstructorArgs()
    {
        return array(
            array(1),
            array(1, null),
            array(1, $this->getMock('\Serializable')),
            array(1, $this->getMock('\Serializable'), true),
            array(1, $this->getMock('\Serializable'), false),
            array(1, $this->getMock('\Serializable'), null, true),
            array(1, $this->getMock('\Serializable'), null, false),
        );
    }

    public function testSerializable()
    {
        $subject = new Configuration(1);
        $subject->setIndex(2);
        $subject->setCreateDirectory(true);
        $subject->setRemoveDirectory(true);
        $subject->setParameters(new Configuration(1));
        $subject->setId(1);

        $data         = serialize($subject);
        $deserialized = unserialize($data);

        $this->assertEquals($subject, $deserialized);
    }
}