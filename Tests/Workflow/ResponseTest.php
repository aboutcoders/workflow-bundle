<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Workflow;

use Abc\Bundle\WorkflowBundle\Workflow\Response;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /** @var SerializerInterface */
    private $serializer;
    /** @var Response */
    private $subject;

    public function setUp()
    {
        $this->subject = new Response();
        $this->serializer = SerializerBuilder::create()->build();
    }

    public function testAdd()
    {
        $this->subject->addAction('foobar');
        $this->assertEquals(array('foobar'), $this->subject->getActions());

        $this->subject->addAction('barfoo');
        $this->assertEquals(array('foobar', 'barfoo'), $this->subject->getActions());
    }

    /**
     * @param mixed $name
     * @dataProvider getInvalidArgs
     * @expectedException \InvalidArgumentException
     */
    public function testAddThrowsInvalidArgumentExceptions($name = null)
    {
        $this->subject->addAction($name);
    }

    public function testRemove()
    {
        $this->subject->addAction('foobar');

        $this->subject->removeAction('foobar');
        $this->assertEmpty($this->subject->getActions());

        $this->subject->addAction('foobar');
        $this->subject->addAction('barfoo');

        $this->subject->removeAction('foobar');

        $this->assertCount(1, $this->subject->getActions());
        $this->assertContains('barfoo', $this->subject->getActions());
    }

    /**
     * @param mixed $name
     * @dataProvider getInvalidArgs
     * @expectedException \InvalidArgumentException
     */
    public function testRemoveThrowsInvalidArgumentExceptions($name = null)
    {
        $this->subject->removeAction($name);
    }

    public function testJsonSerialization()
    {
        $this->subject->addAction('foo');
        $this->subject->addAction('bar');

        $data = $this->serializer->serialize($this->subject, 'json');

        $subject = $this->serializer->deserialize($data, 'Abc\Bundle\WorkflowBundle\Workflow\Response', 'json');

        $this->assertEquals($this->subject, $subject);
    }

    /**
     * @return array
     */
    public static function getInvalidArgs()
    {
        return array(
            array(),
            array(1),
            array(true),
            array(false),
            array(array()),
            array(new \stdClass),
        );
    }
}