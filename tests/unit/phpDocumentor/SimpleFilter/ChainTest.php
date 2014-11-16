<?php

namespace phpDocumentor\SimpleFilter;

use \Mockery as m;

class Foo {}
class ChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Chain
     */
    private $chain;

    public function setUp()
    {
        $this->chain = new Chain();
    }

    public function testAttachFilter()
    {
        /** @var FilterInterface $filterMock */
        $filterMock = m::mock('phpDocumentor\SimpleFilter\FilterInterface');

        $filter = $this->chain->attach($filterMock);
        $this->assertInstanceOf('phpDocumentor\SimpleFilter\Chain', $filter);
    }

    public function testAttachCallback()
    {
        $callback = $this->chain->attach(function () {});
        $filter = $this->chain->attach($callback);
        $this->assertInstanceOf('phpDocumentor\SimpleFilter\Chain', $filter);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAttachInvalidArgument()
    {
        $this->chain->attach(null);
    }

    public function testFilterCallable()
    {
        $value = function () {};
        $a = $this->chain->filter($value);
        $this->assertInstanceOf('phpDocumentor\SimpleFilter\Chain', $a);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testFilterInvalidArgument()
    {
        $chain = new \ReflectionClass($this->chain);
        $innerQueue = $chain->getProperty('innerQueue');
        $innerQueue->setAccessible(true);
        $innerQueue = null;
$foo = new Foo;
        $a = $this->chain->filter($foo);
        var_dump($a);
    }

    public function testCountIncreasesAfterAttach()
    {
        $this->assertCount(0, $this->chain);

        for ($i = 1; $i < 5; $i++) {
            $this->chain->attach(function () {});
            $this->assertCount($i, $this->chain);
        }
    }

    public function testGetIterator()
    {
        $iterator = $this->chain->getIterator();
        $this->assertInstanceOf('SplPriorityQueue', $iterator);
    }
}
